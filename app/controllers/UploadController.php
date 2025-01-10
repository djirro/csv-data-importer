<?php

require_once __DIR__ . '/../models/CatalogModel.php';
require_once __DIR__ . '/../models/FileHelper.php';

class UploadController
{
    public function uploadFile()
    {
        // Проверка на POST запрос и наличие файла
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
                $errors = $fileHelper->processCSV($destinationPath);
                
                if (!empty($errors)) {
                    $this->generateErrorReport($errors, $destinationPath);
                }

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
                'message' => 'Файл не был загружен или неверный метод запроса.',
            ]);
        }
    }

    private function generateErrorReport($errors, $filePath)
    {
        $errorFilePath = __DIR__ . '/../uploads/error_report.csv';
        $file = fopen($errorFilePath, 'w');

        fputcsv($file, ['Code', 'Name', 'Error']);

        foreach ($errors as $error) {
            fputcsv($file, $error);
        }

        fclose($file);

        echo json_encode([
            'status' => 'error',
            'message' => 'Обработаны ошибки, отчет сохранен.',
            'filePath' => $errorFilePath,
        ]);
    }
}
?>