<?php

require_once __DIR__ . '/CatalogModel.php';

class FileHelper
{
    public function processCSV($filePath)
    {
        $errors = [];
        $transactionSuccessful = true;

        if (($handle = fopen($filePath, "r")) !== false) {
            $lineNumber = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $lineNumber++;

                if ($lineNumber == 1) continue; // Пропускаем заголовок

                // Если данные некорректны, добавляем ошибку и продолжаем
                if (count($data) < 2) {
                    $errors[] = ['line' => $lineNumber, 'error' => 'Некорректное количество колонок'];
                    continue;
                }

                list($code, $name) = $data;
                $code = trim($code);
                $name = trim($name);

                // Валидация и обработка
                if (!$this->validateName($name)) {
                    $errors[] = [$code, $name, 'Недопустимые символы в названии'];
                    $transactionSuccessful = false; // Если есть ошибка, не обновляем базу
                } else {
                    $catalogModel = new CatalogModel();
                    if (!$catalogModel->insertOrUpdateRecord($code, $name)) {
                        $transactionSuccessful = false; // Ошибка при вставке
                    }
                }
            }
            fclose($handle);
        } else {
            error_log("Failed to open file: $filePath");
        }

        // Если были ошибки валидации или вставки, не сохраняем изменения в базе
        if (!$transactionSuccessful) {
            $uploadController = new UploadController();
            $uploadController->generateErrorReport($errors, $filePath);
        }

        return $errors;
    }

    private function validateName($name)
    {
        return preg_match('/^[a-zA-Zа-яА-Я0-9 .-]+$/u', $name);
    }
}
