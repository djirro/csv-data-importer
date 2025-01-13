<?php

class ReportSender
{
    // Отправляет отчет с ошибками пользователю.
    public function sendErrorReport($filePath, $originalFileName)
    {
        error_log("sendErrorReport: Проверка существования файла: $filePath");

        if (file_exists($filePath)) {
            error_log("Файл найден: $filePath");

            $fileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '_error_report.csv';
            error_log("Новый файл для скачивания: $fileName");

            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . filesize($filePath));
            error_log("Заголовки отправлены");

            readfile($filePath);
            error_log("Файл отправлен");

            exit;
        } else {
            error_log("Ошибка: Файл не найден: $filePath");

            echo json_encode([
                'status' => 'error',
                'message' => 'Не удалось найти отчет с ошибками.',
            ]);
        }
    }
}