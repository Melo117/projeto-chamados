<?php

header("Content-Type: application/json");

class Categoria
{
    public function __construct()
    {
        session_start();
    }

    public function listarCategorias()
    {
        require_once __DIR__ . '/config/database.php';

        try {
            $sql = "SELECT id, nome, descricao, ativo FROM categorias";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($categorias);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao listar categorias"]);
        }
    }

    public function adicionarCategoria()
    {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode(["error" => "Você precisa ser administrador para adicionar categoria."]);
            return;
        }

        require_once __DIR__ . '/config/database.php';

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nome'])) {
            echo json_encode(["error" => "O campo 'nome' é obrigatório."]);
            return;
        }

        $nome = $data['nome'];
        $descricao = $data['descricao'] ?? null;
        $ativo = $data['ativo'] ?? 1;

        try {
            $sql = "INSERT INTO categorias (nome, descricao, ativo) VALUES (:nome, :descricao, :ativo)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["message" => "Categoria adicionada com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao adicionar categoria"]);
        }
    }

    public function atualizarCategoria(int $id)
    {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode(["error" => "Você precisa ser administrador para atualizar categoria."]);
            return;
        }

        require_once __DIR__ . '/config/database.php';

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nome'])) {
            echo json_encode(["error" => "O campo 'nome' é obrigatório."]);
            return;
        }

        $nome = $data['nome'];
        $descricao = $data['descricao'] ?? null;
        $ativo = $data['ativo'] ?? 1;

        try {
            $sql = "UPDATE categorias SET nome = :nome, descricao = :descricao, ativo = :ativo WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["message" => "Categoria atualizada com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar categoria"]);
        }
    }

    public function deletarCategoria(int $id)
    {
        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode(["error" => "Você precisa ser administrador para deletar categoria."]);
            return;
        }

        require_once __DIR__ . '/config/database.php';

        try {
            $sql = "DELETE FROM categorias WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(["message" => "Categoria deletada com sucesso!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao deletar categoria"]);
        }
    }
}
?>
