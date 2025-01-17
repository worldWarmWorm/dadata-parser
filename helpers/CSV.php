<?php
/**
 * Класс для работы с csv-файлами
 */

class CSV {
    private ?string $_fileRead = null;
    private ?string $_fileWrite = null;

    /**
     * @param string|null $fileRead - путь до csv-файла для чтения
     * @param string|null $fileWrite - путь до csv-файла для записи
     * @throws Exception
     */
    public function __construct(string $fileRead = null, string $fileWrite = null) {
        file_exists($fileRead) ?
            $this->_fileRead = $fileRead :
            throw new Exception("Файл " . $fileRead . " не найден");
        file_exists($fileWrite) ?
            $this->_fileWrite = $fileWrite :
            throw new Exception("Файл " . $fileWrite . " не найден");
    }

    /**
     * Метод для чтения из csv-файла. Возвращает массив с данными из csv
     * @return array;
     */
    public function readCSV(): array {
        $handle = fopen($this->_fileRead, "r");
        $array_line_full = [];
        while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
            $array_line_full[] = $line;
        }
        fclose($handle);

        return $array_line_full;
    }

    /**
     * Метод для записи в csv-файл. Возвращает массив с данными из csv
     */
    public function writeCSV(Array $csv) {
        $handle = fopen($this->_fileWrite, "a");
        foreach ($csv as $value) {
            fputcsv($handle, explode(";", $value), ";");
        }
        fclose($handle);
    }


    /**
     * Показать массив с айдишниками отвергнутых запросов
     * @param $arr - массив содержащий как успешные так и отвергнутые запросы
     * @return array|string;
     */
    public static function getRejectedQueries($arr): array|string {
        $result = 'Сервер ответил на все запросы!';
        if (!empty($arr)) {
            foreach ($arr as $k => $v) {
                if ($v === null) {
                    $rejected[$k] = $v;
                }
            }
            $result = array_keys($rejected);
        }

        return $result;
    }

    /**
     * Показать массив с айдишниками успешных запросов
     * @param $arr - массив содержащий как успешные так и отвергнутые запросы
     * @return array|string;
     */
    public static function getAcceptedQueries($arr): array|string {
        $result = 'Сервер не ответил ни на один запрос!';
        if (!empty($arr)) {
            foreach ($arr as $k => $v) {
                if ($v !== null) {
                    $accepted[$k] = $v;
                }
            }
            $result = array_keys($accepted);
        }

        return $result;
    }

    /**
     * Показать длину массива
     * @param $arr - массив который нужно измерить
     * @return int|string;
     */
    public static function arrayLength($arr): int|string {
        return $arr ? count($arr) : 'Длина массива равна 0';
    }

    /**
     * Выводит данные из массива в документ в читаемом виде
     * @param $arr - массив который нужно вывести
     * @return void;
     */
    public static function prettifyArray($arr): void {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }

    public static function convertToJSON($arr): bool|string {
        return json_encode($arr);
    }
}
