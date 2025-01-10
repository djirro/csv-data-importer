<?php

require_once __DIR__ . '/CatalogModel.php';

class FileHelper
{
    public function processCSV($filePath)
    {
        $errors = [];
        if (($handle = fopen($filePath, "r")) !== false) {
            $lineNumber = 0;

            // Читаем строки файла
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $lineNumber++;

                // Логируем текущую строку
                error_log("Processing line $lineNumber: " . print_r($data, true));

                // Пропускаем заголовок (первую строку)
                if ($lineNumber == 1) continue;

                // Проверяем корректность данных
                if (count($data) < 2) {
                    $errors[] = ['line' => $lineNumber, 'error' => 'Некорректное количество колонок'];
                    continue;
                }

                list($code, $name) = $data;

                // Очищаем пробелы и логируем значения
                $code = trim($code);
                $name = trim($name);
                error_log("Parsed values: code='$code', name='$name'");

                // Валидация и обработка
                if (!$this->validateName($name)) {
                    $errors[] = [$code, $name, 'Недопустимые символы в названии'];
                } else {
                    $catalogModel = new CatalogModel();
                    $catalogModel->insertOrUpdateRecord($code, $name);
                }
            }
            fclose($handle);
        } else {
            error_log("Failed to open file: $filePath");
        }

        return $errors;
    }

    private function validateName($name)
    {
        return preg_match('/^[a-zA-Zа-яА-Я0-9 .-]+$/u', $name);
    }
}
