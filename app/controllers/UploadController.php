<?php

require_once __DIR__ . '/../models/FileHelper.php';
require_once __DIR__ . '/../models/ReportGenerator.php';

class UploadController
{
    /**
     * Обрабатывает загрузку файла и создает отчет об ошибках.
     */
    public function uploadFile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $uploadDir = __DIR__ . '/../uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destinationPath = $uploadDir . basename($fileName);
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                $fileHelper = new FileHelper();
                $errorReport = $fileHelper->processCSV($destinationPath);

                $reportGenerator = new ReportGenerator();
                $reportFile = $reportGenerator->generateErrorReport($errorReport);

                if (count(array_filter($errorReport, fn($error) => !empty($error[2]))) > 0) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Файл загружен с ошибками.',
                        'filePath' => $reportFile,
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Файл успешно загружен и обработан.',
                        'filePath' => $destinationPath,
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Не удалось переместить файл.',
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Файл не был загружен или неверный метод запроса.',
            ]);
        }
    }
}