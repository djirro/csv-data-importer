<?php

class ReportGenerator
{
    /**
     * Генерирует CSV отчет об ошибках.
     * 
     * Проходит по массиву ошибок и записывает данные в файл error_report.csv.
     * В отчете содержатся столбцы: 'code', 'name', 'error'.
     * 
     * @param array $errorReport Массив с ошибками, где каждая ошибка — это массив
     *                           с тремя элементами: код, название и описание ошибки.
     * @return string Путь к созданному отчету.
     */
    public function generateErrorReport($errorReport)
    {
        $uploadDir = __DIR__ . '/../uploads/';
        $reportFile = $uploadDir . 'error_report.csv';

        if (($handle = fopen($reportFile, 'w')) !== false) {
            fputcsv($handle, ['code', 'name', 'error']);

            foreach ($errorReport as $error) {
                fputcsv($handle, $error);
            }

            fclose($handle);
        } else {
            error_log("Не удалось создать отчет: $reportFile");
        }

        return $reportFile;
    }
}