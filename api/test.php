<?php

header("Content-Type: application/json");


require_once "config/database.php";

echo json_encode([
   "status" => "success",
    "message" => "Conexão com o banco de dados estabelecida com sucesso!"
    
]);