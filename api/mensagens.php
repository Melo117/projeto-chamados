<?php

header("Content-Type: application/json");

class Mensagens
{
    public function __construct()
    {
        session_start();
    }

    public function listarMensagens(int $chamado_id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado para listar mensagens."]);
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

            // Verificar permissão: admin, solicitante ou funcionário designado
            $temAcesso = ($_SESSION['perfil'] == 'ADMINISTRADOR') ||
                        ($_SESSION['id'] == $chamado['solicitante_id']) ||
                        ($_SESSION['id'] == $chamado['funcionario_id']);

            if (!$temAcesso) {
                http_response_code(403);
                echo json_encode(["error" => "Você não tem acesso a este chamado."]);
                return;
            }

            $sql = "SELECT m.id, m.mensagem, m.data_hora, m.usuario_id, u.nome as usuario_nome, u.perfil
                    FROM mensagens m
                    INNER JOIN usuarios u ON m.usuario_id = u.id
                    WHERE m.chamado_id = :chamado_id
                    ORDER BY m.data_hora ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
            $stmt->execute();
            $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($mensagens);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao listar mensagens"]);
        }
    }

    public function adicionarMensagem(int $chamado_id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado para adicionar mensagem."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['mensagem'])) {
            echo json_encode(["error" => "O campo 'mensagem' é obrigatório."]);
            return;
        }

        if (trim($data['mensagem']) === '') {
            echo json_encode(["error" => "A mensagem não pode estar vazia."]);
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

            $mensagem = $data['mensagem'];
            $usuario_id = $_SESSION['id'];

            $sql = "INSERT INTO mensagens (chamado_id, usuario_id, mensagem, data_hora)
                    VALUES (:chamado_id, :usuario_id, :mensagem, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':mensagem', $mensagem);
            $stmt->execute();

            // Atualizar data_atualizacao do chamado
            $sqlUpdate = "UPDATE chamados SET data_atualizacao = NOW() WHERE id = :chamado_id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
            $stmtUpdate->execute();

            echo json_encode(["message" => "Mensagem adicionada com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao adicionar mensagem"]);
        }
    }

    public function buscarMensagemPorId(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado."]);
            return;
        }

        try {
            $sql = "SELECT m.id, m.chamado_id, m.mensagem, m.data_hora, m.usuario_id, u.nome as usuario_nome, u.perfil
                    FROM mensagens m
                    INNER JOIN usuarios u ON m.usuario_id = u.id
                    WHERE m.id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $mensagem = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$mensagem) {
                http_response_code(404);
                echo json_encode(["error" => "Mensagem não encontrada."]);
                return;
            }

            echo json_encode($mensagem);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao buscar mensagem"]);
        }
    }

    public function deletarMensagem(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado."]);
            return;
        }

        try {
            // Verificar se a mensagem existe e quem é o autor
            $sqlCheck = "SELECT usuario_id, chamado_id FROM mensagens WHERE id = :id";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtCheck->execute();
            $mensagem = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$mensagem) {
                http_response_code(404);
                echo json_encode(["error" => "Mensagem não encontrada."]);
                return;
            }

            // Verificar permissão: admin ou autor
            $temPermissao = ($_SESSION['perfil'] == 'ADMINISTRADOR') || ($_SESSION['id'] == $mensagem['usuario_id']);

            if (!$temPermissao) {
                http_response_code(403);
                echo json_encode(["error" => "Você não tem permissão para deletar esta mensagem."]);
                return;
            }

            $sql = "DELETE FROM mensagens WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["message" => "Mensagem deletada com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao deletar mensagem"]);
        }
    }
}
?>
