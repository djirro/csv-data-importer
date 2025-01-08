<?php
// Добавляем заголовки CORS для разрешения запросов с другого порта.
header("Access-Control-Allow-Origin: http://localhost:8081");  // фронтенд-сервер
header("Access-Control-Allow-Methods: POST");  // Разрешаем только POST запросы
header("Access-Control-Allow-Headers: Content-Type");  // Разрешаем заголовки для отправки файлов

// Проверяем, что запрос - POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $uploadDir = __DIR__ . '/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destinationPath = $uploadDir . basename($fileName);
        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Файл успешно загружен',
                'filePath' => $destinationPath,
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Не удалось переместить файл.',
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Файл не был загружен.',
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Неверный метод запроса. Используйте POST.',
    ]);
}