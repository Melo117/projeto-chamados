<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Tratamento de requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Mapear as APIs disponíveis
$apis = [
    'usuario' => 'Usuario',
    'usuarios' => 'Usuario',
    'categoria' => 'Categoria',
    'categorias' => 'Categoria',
    'funcionarios' => 'Funcionarios',
    'chamados' => 'Chamados',
    'mensagens' => 'Mensagens',
    'anexos' => 'Anexos'
];

try {
    // Obter a ação requisitada (format: "api.metodo" ou "api/metodo")
    $action = isset($_GET['action']) ? $_GET['action'] : $_SERVER['PATH_INFO'] ?? '/';

    // Normalizar a ação (remover / e converter . em / se necessário)
    $action = trim($action, '/');
    $parts = explode('.', str_replace('/', '.', $action));

    $queryParams = $_GET;
    unset($queryParams['action']);

    foreach ($queryParams as $key => $value) {
        $keyNormalized = strtolower($key);
        if (in_array($keyNormalized, ['id', 'chamado_id', 'usuario_id', 'categoria_id', 'funcionario_id', 'matricula'], true)) {
            $parts[] = $value;
        }
    }

    if (count($parts) < 2) {
        http_response_code(400);
        echo json_encode(['error' => 'Ação inválida. Use: ?action=api.metodo']);
        exit();
    }

    $apiName = strtolower($parts[0]);
    $methodName = $parts[1];

    // Validar se a API existe
    if (!isset($apis[$apiName])) {
        http_response_code(404);
        echo json_encode(['error' => 'API não encontrada: ' . $apiName]);
        exit();
    }
    
    // Incluir e instanciar a classe da API
    $apiClass = $apis[$apiName];
    $apiFile = __DIR__ . '/' . $apiName . '.php';
    
    if (!file_exists($apiFile)) {
        http_response_code(404);
        echo json_encode(['error' => 'Arquivo da API não encontrado']);
        exit();
    }
    
    require_once $apiFile;
    
    // Verificar se a classe existe
    if (!class_exists($apiClass)) {
        http_response_code(500);
        echo json_encode(['error' => 'Classe não encontrada: ' . $apiClass]);
        exit();
    }
    
    // Instanciar a classe
    $api = new $apiClass();
    
    // Verificar se o método existe
    if (!method_exists($api, $methodName)) {
        http_response_code(404);
        echo json_encode(['error' => 'Método não encontrado: ' . $methodName]);
        exit();
    }
    
    // Extrair parâmetros da URL (para métodos que precisam de ID ou outros params)
    $additionalParams = array_slice($parts, 2);
    
    // Chamar o método com os parâmetros
    if (!empty($additionalParams)) {
        // Se houver parâmetros adicionais, passá-los como argumentos
        $reflection = new ReflectionMethod($apiClass, $methodName);
        $params = $reflection->getParameters();
        
        $callParams = [];
        foreach ($params as $i => $param) {
            $callParams[] = $additionalParams[$i] ?? null;
        }
        
        call_user_func_array([$api, $methodName], $callParams);
    } else {
        // Caso contrário, apenas chamar o método
        $api->$methodName();
    }

} catch (ReflectionException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao processar requisição: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno: ' . $e->getMessage()]);
}
?>
