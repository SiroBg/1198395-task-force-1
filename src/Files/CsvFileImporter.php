<?php

namespace TaskForce\Files;

use RuntimeException;
use SplFileObject;
use app\exceptions\SourceFileException;

/**
 * Класс, отвечающий за получение данных в формате CSV из файла.
 */
class CsvFileImporter
{
    protected string $filePath;
    protected SplFileObject $fileObj;
    public CsvFileData $data {
        get => $this->data;
    }

    /**
     * Создаёт экземпляр класса CsvFileParser.
     *
     * @param string $filePath Путь к файлу.
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Импортирует данные из .csv файла.
     *
     * @throws SourceFileException Исключение при обращении к файлу с данными.
     */
    public function import(): void
    {
        if (!file_exists($this->filePath)) {
            throw new SourceFileException(
                'Переданный файл ' . $this->filePath . ' не существует'
            );
        }

        try {
            $this->fileObj = new SplFileObject($this->filePath);
        } catch (RuntimeException $exception) {
            throw new SourceFileException(
                'Не удалось открыть заданный файл' . $exception->getMessage()
            );
        }

        $this->data = new CsvFileData(
            $this->fileObj->getBasename('.csv'),
            $this->getHeaderData(),
        );

        foreach ($this->getNextLine() as $line) {
            if (count($line) !== count($this->data->columns)) {
                throw new SourceFileException(
                    'В файле ' . $this->filePath
                    . ' количество столбцов не совпадает с количеством значений',
                );
            }
            $this->data->values = $line;
        }
    }

    /**
     * Получает заголовки значений из файла.
     *
     * Удаляет BOM (byte order mark), если он присутствует в начале файла.
     *
     * @return array|null Массив с заголовками, либо null при пустом файле.
     */
    private function getHeaderData(): ?array
    {
        $bom = pack('H*', 'EFBBBF');
        if (!preg_match("/^$bom/", $this->fileObj->fread(3))) {
            $this->fileObj->rewind();
        }

        return $this->fileObj->fgetcsv(escape: '');
    }

    /**
     * Получает следующую строчку значений из .csv файла.
     *
     * @return iterable|null Строка со значениями, либо null при достижении конца файла.
     */
    private function getNextLine(): ?iterable
    {
        while (!$this->fileObj->eof()) {
            yield $this->fileObj->fgetcsv(escape: '');
        }

        return null;
    }
}
