<?php
// Разрешаем доступ с любого источника.
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Если запрос типа OPTIONS, сразу возвращаем успешный ответ.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Подключаем контроллер для обработки загрузки файла.
if ($_SERVER['REQUEST_URI'] === '/upload') {
    require_once __DIR__ . '/../app/controllers/UploadController.php';
    $controller = new UploadController();
    $controller->uploadFile();
    exit;
}
?>