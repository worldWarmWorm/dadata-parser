<?php
/**
 * Класс для работы с csv-файлами
 */

class CSV {
    private ?string $_fileRead = null;
    private ?string $_fileWrite = null;

    /**
     * @param string $fileRead  - путь до csv-файла для чтения
     * @param string $fileWrite  - путь до csv-файла для записи
     */
    public function __construct(string $fileRead, string $fileWrite) {
        file_exists($fileRead) ? $this->_fileRead = $fileRead : throw new Exception("Файл " . $fileRead . " не найден");
        file_exists($fileWrite) ? $this->_fileWrite = $fileWrite : throw new Exception("Файл " . $fileWrite . " не найден");
    }

    /**
     * Метод для чтения из csv-файла. Возвращает массив с данными из csv
     * @return array;
     */
    public function readCSV() {
        $handle = fopen($this->_fileRead, "r");

        $array_line_full = [];
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            $array_line_full[] = $line;
        }
        fclose($handle);
        return $array_line_full;
    }

    /**
     * Метод для записи в csv-файл. Возвращает массив с данными из csv
     * @return array;
     */
    public function writeCSV(Array $csv) {
        $handle = fopen($this->_fileWrite, "a");

        foreach ($csv as $value) {
            fputcsv($handle, explode(";", $value), ";");
        }
        fclose($handle);
    }
}
