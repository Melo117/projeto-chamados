<?php

header("Content-Type: application/json");

class Chamados
{
    private $statusValidos = ['ABERTO', 'EM_ANALISE', 'EM_ATENDIMENTO', 'RESOLVIDO', 'FECHADO', 'CANCELADO', 'RECUSADO'];

    public function __construct()
    {
        session_start();
    }

    public function listarChamados()
    {
        require_once __DIR__ . '/config/database.php';

        try {
            $sql = "SELECT c.id, c.numero, c.nome, c.descricao, c.categoria_id, c.solicitante_id, 
                           c.status, c.data_abertura, c.data_atualizacao, c.data_resolucao, c.data_fechamento,
                           c.funcionario_id, u.nome as solicitante_nome, cat.nome as categoria_nome
                    FROM chamados c
                    LEFT JOIN usuarios u ON c.solicitante_id = u.id
                    LEFT JOIN categorias cat ON c.categoria_id = cat.id
                    ORDER BY c.data_abertura DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($chamados);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao listar chamados"]);
        }
    }

    public function buscarChamadoPorId(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        try {
            $sql = "SELECT c.id, c.numero, c.nome, c.descricao, c.categoria_id, c.solicitante_id, 
                           c.status, c.data_abertura, c.data_atualizacao, c.data_resolucao, c.data_fechamento,
                           c.funcionario_id, u.nome as solicitante_nome, cat.nome as categoria_nome,
                           f.nome as funcionario_nome
                    FROM chamados c
                    LEFT JOIN usuarios u ON c.solicitante_id = u.id
                    LEFT JOIN categorias cat ON c.categoria_id = cat.id
                    LEFT JOIN funcionarios func ON c.funcionario_id = func.id
                    LEFT JOIN usuarios f ON func.usuario_id = f.id
                    WHERE c.id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $chamado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$chamado) {
                http_response_code(404);
                echo json_encode(["error" => "Chamado não encontrado."]);
                return;
            }

            echo json_encode($chamado);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao buscar chamado"]);
        }
    }

    public function novoChamado()
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado para criar um chamado."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['numero']) || !isset($data['nome']) || !isset($data['descricao']) || !isset($data['categoria_id'])) {
            echo json_encode(["error" => "Os campos 'numero', 'nome', 'descricao' e 'categoria_id' são obrigatórios."]);
            return;
        }

        $numero = $data['numero'];
        $nome = $data['nome'];
        $descricao = $data['descricao'];
        $categoria_id = $data['categoria_id'];
        $solicitante_id = $_SESSION['id'];
        $funcionario_id = $data['funcionario_id'] ?? null;
        $status = 'ABERTO';

        try {
            $sql = "INSERT INTO chamados (numero, nome, descricao, categoria_id, solicitante_id, funcionario_id, status, data_abertura, data_atualizacao)
                    VALUES (:numero, :nome, :descricao, :categoria_id, :solicitante_id, :funcionario_id, :status, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
            $stmt->bindParam(':solicitante_id', $solicitante_id, PDO::PARAM_INT);
            $stmt->bindParam(':funcionario_id', $funcionario_id);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            echo json_encode(["message" => "Chamado criado com sucesso!", "numero" => $numero]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao criar chamado"]);
        }
    }

    public function atualizarChamado(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado para atualizar um chamado."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nome']) && !isset($data['descricao']) && !isset($data['status']) && !isset($data['funcionario_id'])) {
            echo json_encode(["error" => "Forneça pelo menos um campo para atualizar."]);
            return;
        }

        if (isset($data['status']) && !in_array($data['status'], $this->statusValidos)) {
            echo json_encode(["error" => "Status inválido. Valores aceitos: " . implode(", ", $this->statusValidos)]);
            return;
        }

        try {
            $updates = [];
            $params = [':id' => $id];

            if (isset($data['nome'])) {
                $updates[] = "nome = :nome";
                $params[':nome'] = $data['nome'];
            }

            if (isset($data['descricao'])) {
                $updates[] = "descricao = :descricao";
                $params[':descricao'] = $data['descricao'];
            }

            if (isset($data['status'])) {
                $updates[] = "status = :status";
                $params[':status'] = $data['status'];
            }

            if (isset($data['funcionario_id'])) {
                $updates[] = "funcionario_id = :funcionario_id";
                $params[':funcionario_id'] = $data['funcionario_id'];
            }

            $updates[] = "data_atualizacao = NOW()";

            $sql = "UPDATE chamados SET " . implode(", ", $updates) . " WHERE id = :id";
            $stmt = $pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            echo json_encode(["message" => "Chamado atualizado com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar chamado"]);
        }
    }

    public function deletarChamado(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode(["error" => "Você precisa ser administrador para deletar chamado."]);
            return;
        }

        try {
            $sql = "DELETE FROM chamados WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["message" => "Chamado deletado com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao deletar chamado"]);
        }
    }
}
?>
