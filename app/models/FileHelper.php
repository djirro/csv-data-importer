<?php

require_once __DIR__ . '/CatalogModel.php';

class FileHelper
{
    /**
     * Обрабатывает CSV файл: читает данные, валидирует и добавляет в базу.
     * Генерирует отчет с ошибками для строк с недопустимыми данными.
     * 
     * @param string $filePath Путь к CSV файлу для обработки.
     * @return array Массив с ошибками, содержащий код, название и описание ошибки.
     */
    public function processCSV($filePath)
    {
        $errorReport = [];
        $validRows = [];

        if (($handle = fopen($filePath, "r")) !== false) {
            $lineNumber = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $lineNumber++;

                if ($lineNumber == 1) continue;

                if (count($data) < 2) {
                    $errorReport[] = [$data[0], $data[1], 'Некорректное количество колонок'];
                    continue;
                }

                list($code, $name) = $data;
                $code = trim($code);
                $name = trim($name);

                $error = '';
                if (!$this->validateName($name)) {
                    $error = 'Недопустимые символы в названии';
                }

                $errorReport[] = [$code, $name, $error];

                if (empty($error)) {
                    $validRows[] = [$code, $name];
                }
            }
            fclose($handle);
        } else {
            error_log("Failed to open file: $filePath");
        }

        $catalogModel = new CatalogModel();
        foreach ($validRows as $row) {
            list($code, $name) = $row;
            if (!$catalogModel->insertOrUpdateRecord($code, $name)) {
                error_log("Ошибка при добавлении записи с кодом $code и названием $name в базу данных.");
            }
        }

        return $errorReport;
    }

    // Проверяет, содержит ли название только допустимые символы.
    private function validateName($name)
    {
        return preg_match('/^[a-zA-Zа-яА-Я0-9 .-]+$/u', $name);
    }
}