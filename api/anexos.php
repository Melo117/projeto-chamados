<?php

header("Content-Type: application/json");

class Anexos
{
    private $uploadDir = __DIR__ . '/../uploads/';
    private $tiposPermitidos = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip'];
    private $tamanhoMaximo = 5242880; // 5MB em bytes

    public function __construct()
    {
        session_start();
    }

    public function listarAnexos(int $chamado_id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado para listar anexos."]);
            return;
        }

        try {
            // Verificar se o usuário tem acesso ao chamado
            $sqlCheck = "SELECT solicitante_id, funcionario_id FROM chamados WHERE id = :chamado_id";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
            $stmtCheck->execute();
            $chamado = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$chamado) {
                http_response_code(404);
                echo json_encode(["error" => "Chamado não encontrado."]);
                return;
            }

            // Verificar permissão
            $temAcesso = ($_SESSION['perfil'] == 'ADMINISTRADOR') ||
                        ($_SESSION['id'] == $chamado['solicitante_id']) ||
                        ($_SESSION['id'] == $chamado['funcionario_id']);

            if (!$temAcesso) {
                http_response_code(403);
                echo json_encode(["error" => "Você não tem acesso a este chamado."]);
                return;
            }

            $sql = "SELECT a.id, a.nome_arquivo, a.caminho_arquivo, a.tipo_arquivo, a.tamanho, a.data_upload, a.usuario_id, u.nome as usuario_nome
                    FROM anexos a
                    INNER JOIN usuarios u ON a.usuario_id = u.id
                    WHERE a.chamado_id = :chamado_id
                    ORDER BY a.data_upload DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
            $stmt->execute();
            $anexos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($anexos);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao listar anexos"]);
        }
    }

    public function buscarAnexoPorId(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado."]);
            return;
        }

        try {
            $sql = "SELECT a.id, a.chamado_id, a.nome_arquivo, a.caminho_arquivo, a.tipo_arquivo, a.tamanho, a.data_upload, a.usuario_id, u.nome as usuario_nome
                    FROM anexos a
                    INNER JOIN usuarios u ON a.usuario_id = u.id
                    WHERE a.id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $anexo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$anexo) {
                http_response_code(404);
                echo json_encode(["error" => "Anexo não encontrado."]);
                return;
            }

            echo json_encode($anexo);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao buscar anexo"]);
        }
    }

    public function adicionarAnexo(int $chamado_id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado para fazer upload."]);
            return;
        }

        // Este método espera um arquivo enviado via multipart/form-data
        if (!isset($_FILES['arquivo'])) {
            echo json_encode(["error" => "Nenhum arquivo foi enviado."]);
            return;
        }

        $arquivo = $_FILES['arquivo'];

        // Validações
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(["error" => "Erro ao fazer upload do arquivo."]);
            return;
        }

        if ($arquivo['size'] > $this->tamanhoMaximo) {
            echo json_encode(["error" => "Arquivo muito grande. Máximo: 5MB"]);
            return;
        }

        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extensao, $this->tiposPermitidos)) {
            echo json_encode(["error" => "Tipo de arquivo não permitido. Tipos aceitos: " . implode(", ", $this->tiposPermitidos)]);
            return;
        }

        try {
            // Verificar se o chamado existe e se o usuário tem acesso
            $sqlCheck = "SELECT solicitante_id, funcionario_id FROM chamados WHERE id = :chamado_id";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
            $stmtCheck->execute();
            $chamado = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$chamado) {
                http_response_code(404);
                echo json_encode(["error" => "Chamado não encontrado."]);
                return;
            }

            // Verificar permissão
            $temAcesso = ($_SESSION['perfil'] == 'ADMINISTRADOR') ||
                        ($_SESSION['id'] == $chamado['solicitante_id']) ||
                        ($_SESSION['id'] == $chamado['funcionario_id']);

            if (!$temAcesso) {
                http_response_code(403);
                echo json_encode(["error" => "Você não tem acesso a este chamado."]);
                return;
            }

            // Criar diretório de upload se não existir
            if (!is_dir($this->uploadDir)) {
                mkdir($this->uploadDir, 0755, true);
            }

            // Gerar nome único para o arquivo
            $nomeArquivo = $arquivo['name'];
            $caminhoArquivo = $this->uploadDir . date('Ymd_His_') . $nomeArquivo;

            // Mover arquivo para pasta de uploads
            if (!move_uploaded_file($arquivo['tmp_name'], $caminhoArquivo)) {
                echo json_encode(["error" => "Erro ao salvar o arquivo no servidor."]);
                return;
            }

            // Salvar informações no banco de dados
            $usuario_id = $_SESSION['id'];
            $tipo_arquivo = mime_content_type($caminhoArquivo) ?? $arquivo['type'];
            $tamanho = filesize($caminhoArquivo);

            $sql = "INSERT INTO anexos (chamado_id, usuario_id, nome_arquivo, caminho_arquivo, tipo_arquivo, tamanho, data_upload)
                    VALUES (:chamado_id, :usuario_id, :nome_arquivo, :caminho_arquivo, :tipo_arquivo, :tamanho, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':nome_arquivo', $nomeArquivo);
            $stmt->bindParam(':caminho_arquivo', $caminhoArquivo);
            $stmt->bindParam(':tipo_arquivo', $tipo_arquivo);
            $stmt->bindParam(':tamanho', $tamanho, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["message" => "Arquivo enviado com sucesso!", "nome_arquivo" => $nomeArquivo]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao adicionar anexo"]);
        }
    }

    public function deletarAnexo(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado."]);
            return;
        }

        try {
            // Verificar se o anexo existe e quem é o autor
            $sqlCheck = "SELECT usuario_id, caminho_arquivo FROM anexos WHERE id = :id";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtCheck->execute();
            $anexo = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$anexo) {
                http_response_code(404);
                echo json_encode(["error" => "Anexo não encontrado."]);
                return;
            }

            // Verificar permissão: admin ou autor
            $temPermissao = ($_SESSION['perfil'] == 'ADMINISTRADOR') || ($_SESSION['id'] == $anexo['usuario_id']);

            if (!$temPermissao) {
                http_response_code(403);
                echo json_encode(["error" => "Você não tem permissão para deletar este anexo."]);
                return;
            }

            // Deletar arquivo fisicamente se existir
            if (file_exists($anexo['caminho_arquivo'])) {
                unlink($anexo['caminho_arquivo']);
            }

            // Deletar registro do banco
            $sql = "DELETE FROM anexos WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["message" => "Anexo deletado com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao deletar anexo"]);
        }
    }

    public function downloadAnexo(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado."]);
            return;
        }

        try {
            $sql = "SELECT a.id, a.chamado_id, a.nome_arquivo, a.caminho_arquivo FROM anexos a WHERE a.id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $anexo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$anexo) {
                http_response_code(404);
                echo json_encode(["error" => "Anexo não encontrado."]);
                return;
            }

            // Verificar se o usuário tem acesso ao chamado
            $sqlCheck = "SELECT solicitante_id, funcionario_id FROM chamados WHERE id = :chamado_id";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':chamado_id', $anexo['chamado_id'], PDO::PARAM_INT);
            $stmtCheck->execute();
            $chamado = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            $temAcesso = ($_SESSION['perfil'] == 'ADMINISTRADOR') ||
                        ($_SESSION['id'] == $chamado['solicitante_id']) ||
                        ($_SESSION['id'] == $chamado['funcionario_id']);

            if (!$temAcesso) {
                http_response_code(403);
                echo json_encode(["error" => "Você não tem acesso a este arquivo."]);
                return;
            }

            // Fazer download do arquivo
            if (file_exists($anexo['caminho_arquivo'])) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $anexo['nome_arquivo'] . '"');
                header('Content-Length: ' . filesize($anexo['caminho_arquivo']));
                readfile($anexo['caminho_arquivo']);
                exit;
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Arquivo não encontrado no servidor."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao fazer download do arquivo"]);
        }
    }
}
?>
