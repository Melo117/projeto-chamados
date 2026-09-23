<?php

header("Content-Type: application/json");

class Usuario
{
    public function __construct()
    {
        session_start();
    }

    public function logar()
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_POST['email']) || !isset($_POST['senha'])) {
            echo json_encode(["error" => "Email e senha são obrigatórios"]);
            return;
        }

        if (strlen($_POST['senha']) < 6) {
            echo json_encode(["error" => "A senha deve ter no mínimo 6 caracteres"]);
            return;
        }

        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($_POST['senha'], $usuario['senha'])) {

                session_regenerate_id(true);

                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['perfil'] = $usuario['perfil'];

                echo json_encode(["message" => "Login bem-sucedido"]);
            } else {
                echo json_encode(["error" => "Email ou senha inválidos"]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                "error" => "Erro ao realizar login. Por favor tente novamente mais tarde."
            ]);
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        echo json_encode(["message" => "Logout bem-sucedido"]);
    }

    public function cadastrarUsuario()
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode([
                "error" => "Você precisa ser o administrador para cadastrar usuários."
            ]);
            return;
        }

        if (
            !isset($_POST['nome']) ||
            !isset($_POST['email']) ||
            !isset($_POST['senha']) ||
            !isset($_POST['perfil']) ||
            !isset($_POST['status'])
        ) {
            echo json_encode(["error" => "Todos os campos são obrigatórios"]);
            return;
        }

        if (strlen($_POST['senha']) < 6) {
            echo json_encode([
                "error" => "A senha deve ter no mínimo 6 caracteres"
            ]);
            return;
        }

        $perfisValidos = ['ADMINISTRADOR', 'ATENDENTE', 'SOLICITANTE'];
        if (!in_array($_POST['perfil'], $perfisValidos)) {
            echo json_encode([
                "error" => "Perfil inválido. Valores aceitos: " . implode(", ", $perfisValidos)
            ]);
            return;
        }

        $statusValidos = ['ATIVO', 'INATIVO'];
        if (!in_array($_POST['status'], $statusValidos)) {
            echo json_encode([
                "error" => "Status inválido. Valores aceitos: " . implode(", ", $statusValidos)
            ]);
            return;
        }

        try {

            $sql = "INSERT INTO usuarios
                    (nome, email, senha, perfil, status, data_criacao, data_atualizacao)
                    VALUES
                    (:nome, :email, :senha, :perfil, :status, NOW(), NOW())";

            $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':nome', $_POST['nome']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':senha', $senha);
            $stmt->bindParam(':perfil', $_POST['perfil']);
            $stmt->bindParam(':status', $_POST['status']);

            $stmt->execute();

            echo json_encode([
                "message" => "Usuário cadastrado com sucesso"
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "error" => "Erro ao cadastrar usuário"
            ]);
        }
    }

    public function listarUsuarios()
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode([
                "error" => "Você precisa ser o administrador para visualizar todos os usuários."
            ]);
            return;
        }

        try {


            $sql = "SELECT id, nome, email, perfil, status, data_criacao, data_atualizacao
                    FROM usuarios";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($usuarios);
        } catch (PDOException $e) {
            echo json_encode([
                "error" => "Não foi possível listar os usuários: " . $e->getMessage()
            ]);
        }

    }


    public function buscarUsuarioPorId(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (
            !isset($_SESSION['perfil']) ||
            (
                $_SESSION['perfil'] != 'ADMINISTRADOR' &&
                $_SESSION['id'] != $id
            )
        ) {
            echo json_encode([
                "error" => "Você não tem permissão para acessar este usuário."
            ]);
            return;
        }

        try {

            // Não retorna a senha
            $sql = "SELECT id, nome, email, perfil, status, data_criacao, data_atualizacao
                    FROM usuarios
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                echo json_encode($usuario);
            } else {
                echo json_encode([
                    "error" => "Usuário não encontrado"
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                "error" => "Erro ao buscar usuário"
            ]);
        }
    }

    public function atualizarUsuario(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (
            !isset($_SESSION['perfil']) ||
            (
                $_SESSION['perfil'] != 'ADMINISTRADOR' &&
                $_SESSION['id'] != $id
            )
        ) {
            echo json_encode([
                "error" => "Você não tem permissão para atualizar este usuário."
            ]);
            return;
        }

        try {

            if ($_SESSION['perfil'] == 'ADMINISTRADOR') {

                $perfisValidos = ['ADMINISTRADOR', 'ATENDENTE', 'SOLICITANTE'];
                if (isset($_POST['perfil']) && !in_array($_POST['perfil'], $perfisValidos)) {
                    echo json_encode([
                        "error" => "Perfil inválido. Valores aceitos: " . implode(", ", $perfisValidos)
                    ]);
                    return;
                }

                $statusValidos = ['ATIVO', 'INATIVO'];
                if (isset($_POST['status']) && !in_array($_POST['status'], $statusValidos)) {
                    echo json_encode([
                        "error" => "Status inválido. Valores aceitos: " . implode(", ", $statusValidos)
                    ]);
                    return;
                }

                $sql = "UPDATE usuarios
                        SET nome = :nome,
                            email = :email,
                            senha = :senha,
                            perfil = :perfil,
                            status = :status
                        WHERE id = :id";

                $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(':nome', $_POST['nome']);
                $stmt->bindParam(':email', $_POST['email']);
                $stmt->bindParam(':senha', $senha);
                $stmt->bindParam(':perfil', $_POST['perfil']);
                $stmt->bindParam(':status', $_POST['status']);
                $stmt->bindParam(':id', $id);
            } else {

                // Usuário comum não pode alterar perfil nem status
                $sql = "UPDATE usuarios
                        SET nome = :nome,
                            email = :email,
                            senha = :senha
                        WHERE id = :id";

                $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(':nome', $_POST['nome']);
                $stmt->bindParam(':email', $_POST['email']);
                $stmt->bindParam(':senha', $senha);
                $stmt->bindParam(':id', $id);
            }

            $stmt->execute();

            echo json_encode([
                "message" => "Usuário atualizado com sucesso"
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "error" => "Erro ao atualizar usuário"
            ]);
        }
    }

    public function deletarUsuario(int $id)
    {
        require_once __DIR__ . '/config/database.php';

        if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'ADMINISTRADOR') {
            echo json_encode([
                "error" => "Somente o administrador pode deletar usuários."
            ]);
            return;
        }

        try {

            $sql = "DELETE FROM usuarios WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            echo json_encode([
                "message" => "Usuário deletado com sucesso"
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "error" => "Erro ao deletar usuário"
            ]);
        }
    }


}
