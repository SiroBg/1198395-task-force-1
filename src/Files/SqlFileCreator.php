<?php

namespace TaskForce\Files;

use RuntimeException;
use SplFileObject;

use TaskForce\Exceptions\DestinationFileException;

/**
 * Создатель sql файла с запросами на вставку значений в таблицу базы данных.
 */
class SqlFileCreator
{
    protected CsvFileData $data;
    protected string $filePath;
    protected string $dbName;
    protected SplFileObject $fileObj;

    /**
     * Создаёт экземпляр класса SqlFileCreator.
     *
     * @param string      $fileName Название файла без расширения.
     * @param string      $dirPath  Путь к папке, где создастся файл.
     * @param CsvFileData $data     Объект с данными таблицы БД.
     */
    public function __construct(
        string $fileName,
        string $dirPath,
        CsvFileData $data,
        string $dbName
    ) {
        $this->filePath = $dirPath . $fileName . '.sql';
        $this->data = $data;
        $this->dbName = $dbName;
    }

    /**
     * Создаёт файл с sql запросами.
     *
     * @return void
     * @throws DestinationFileException Исключение при ошибке доступа к файлу.
     */
    public function create(): void
    {
        if (file_exists($this->filePath)) {
            throw new DestinationFileException(
                'Переданный файл ' . $this->filePath . ' уже существует',
            );
        }

        try {
            $this->fileObj = new SplFileObject(
                $this->filePath,
                'w',
            );
        } catch (RuntimeException $exception) {
            throw new DestinationFileException(
                'Не удалось создать заданный файл'
                . $exception->getMessage(),
            );
        }

        $this->fileObj->fwrite(
            $this->addDbName() . $this->addSets() . $this->addInsertQuery()
            . $this->addColumns() . $this->addValues(),
        );
    }

    private function addDbName(): string
    {
        return 'USE `' . $this->dbName . "`;\n";
    }

    private function addSets(): string
    {
        return "SET NAMES 'utf8mb4';\n"
            . "SET CHARACTER SET 'utf8mb4';\n";
    }

    /**
     * Добавляет ключевые слова и название таблицы.
     *
     * @return string Строка вставки с названием таблицы.
     */
    private function addInsertQuery(): string
    {
        return 'INSERT INTO `' . $this->data->name . '` ';
    }

    /**
     * Добавляет названия рядов таблицы.
     *
     * @return string Строка с названиями рядов таблицы.
     */
    private function addColumns(): string
    {
        $result = '(';
        $isFirst = true;
        foreach ($this->data->columns as $column) {
            if ($isFirst) {
                $result .= '`' . $column . '`';
                $isFirst = false;
            } else {
                $result .= ', `' . $column . '`';
            }
        }
        $result .= ")\n";
        return $result;
    }

    /**
     * Добавляет значения рядов таблицы.
     *
     * @return string Строка со значениями рядов таблицы.
     */
    private function addValues(): string
    {
        $result = 'VALUES ';
        $isFirst = true;
        foreach ($this->data->values as $value) {
            if ($isFirst) {
                $result .= '(';
                $isFirst = false;
            } else {
                $result .= ",\n (";
            }
            $isFirstRow = true;
            foreach ($value as $row) {
                if ($isFirstRow) {
                    $result .= '\'' . $row . '\'';
                    $isFirstRow = false;
                } else {
                    $result .= ', \'' . $row . '\'';
                }
            }
            $result .= ')';
        }
        $result .= ';';
        return $result;
    }
}
