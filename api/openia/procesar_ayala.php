<?php
header("Access-Control-Allow-Origin: https://ayala.gob.mx");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");
    

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
    exit;
}

$message = trim($_POST['message'] ?? '');

if ($message === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'El mensaje es obligatorio'
    ]);
    exit;
}

/*
    IMPORTANTE:
    NO dejes la API key escrita aquí en producción.
    Muévela a un archivo de configuración o variable de entorno.
*/
$apiKey = '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'; // Reemplaza 'tu_clave_api' con tu propia clave API


$informacionDelRol = <<<PROMPT
Eres un asistente virtual institucional del Ayuntamiento de Ayala.
Responde de forma clara, amable, breve y útil.
Si no conoces una respuesta, indícalo con honestidad.

Información institucional base:
- El asistente atiende preguntas de ciudadanos y visitantes.
- Puede orientar sobre servicios, convocatorias, información general y apoyo institucional.
- Debe responder siempre en español.
- Debe evitar inventar información.
PROMPT;

$data = [
    'model' => 'gpt-4o-mini',
    'messages' => [
        ['role' => 'system', 'content' => $informacionDelRol],
        ['role' => 'user', 'content' => $message]
    ],
    'max_tokens' => 350,
    'temperature' => 0.7
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'No fue posible conectar con el servicio de IA'
    ]);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$decoded = json_decode($response, true);

if ($httpCode >= 400) {
    http_response_code($httpCode);
    echo json_encode([
        'success' => false,
        'error' => $decoded['error']['message'] ?? 'Error al consultar el servicio'
    ]);
    exit;
}

$reply = $decoded['choices'][0]['message']['content'] ?? null;

if (!$reply) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'No se recibió contenido válido desde la IA'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'reply' => trim($reply)
], JSON_UNESCAPED_UNICODE);