<?php 
 
/* 
configuraçoes do banco

1- conectar o banco de dados
2- verificar se o banco de dados existe
3- confirmar se esta conectado ao banco de dados

*/

$host = "localhost";
$dbname = "db_chamdos";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {

http_response_code(500);
    echo json_encode([
        "erro" => "Erro ao conectar ao banco de dados: " . $e->getMessage()
    ]);

    exit;
}