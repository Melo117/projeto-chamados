<?php

header("Content-Type: application/json");

class Funcionarios
{
    public function __construct()
    {
        session_start();
    }

    public function listarFuncionarios()
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode(["error" => "Você precisa ser administrador para listar funcionários."]);
            return;
        }

        try {
            $sql = "SELECT f.id, f.usuario_id, f.matricula, f.ativo, u.nome, u.email, u.perfil
                    FROM funcionarios f
                    INNER JOIN usuarios u ON f.usuario_id = u.id
                    ORDER BY u.nome";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($funcionarios);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao listar funcionários"]);
        }
    }

    public function buscarFuncionarioPorId(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode(["error" => "Você precisa ser administrador para buscar funcionários."]);
            return;
        }

        try {
            $sql = "SELECT f.id, f.usuario_id, f.matricula, f.ativo, u.nome, u.email, u.perfil
                    FROM funcionarios f
                    INNER JOIN usuarios u ON f.usuario_id = u.id
                    WHERE f.id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$funcionario) {
                http_response_code(404);
                echo json_encode(["error" => "Funcionário não encontrado."]);
                return;
            }

            echo json_encode($funcionario);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao buscar funcionário"]);
        }
    }

    public function adicionarFuncionario()
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode(["error" => "Você precisa ser administrador para adicionar funcionário."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['usuario_id']) || !isset($data['matricula'])) {
            echo json_encode(["error" => "Os campos 'usuario_id' e 'matricula' são obrigatórios."]);
            return;
        }

        $usuario_id = $data['usuario_id'];
        $matricula = $data['matricula'];
        $ativo = $data['ativo'] ?? 1;

        try {
            $sql = "INSERT INTO funcionarios (usuario_id, matricula, ativo) VALUES (:usuario_id, :matricula, :ativo)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':matricula', $matricula);
            $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["message" => "Funcionário adicionado com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao adicionar funcionário"]);
        }
    }

    public function atualizarFuncionario(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode(["error" => "Você precisa ser administrador para atualizar funcionário."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['matricula']) && !isset($data['ativo'])) {
            echo json_encode(["error" => "Forneça 'matricula' ou 'ativo' para atualizar."]);
            return;
        }

        try {
            $updates = [];
            $params = [':id' => $id];

            if (isset($data['matricula'])) {
                $updates[] = "matricula = :matricula";
                $params[':matricula'] = $data['matricula'];
            }

            if (isset($data['ativo'])) {
                $updates[] = "ativo = :ativo";
                $params[':ativo'] = $data['ativo'];
            }

            $sql = "UPDATE funcionarios SET " . implode(", ", $updates) . " WHERE id = :id";
            $stmt = $pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            echo json_encode(["message" => "Funcionário atualizado com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar funcionário"]);
        }
    }

    public function deletarFuncionario(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode(["error" => "Você precisa ser administrador para deletar funcionário."]);
            return;
        }

        try {
            $sql = "DELETE FROM funcionarios WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["message" => "Funcionário deletado com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao deletar funcionário"]);
        }
    }
}
?>
