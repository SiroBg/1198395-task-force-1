<?php

namespace TaskForce\Files;

use FilesystemIterator;
use RuntimeException;
use SplFileInfo;
use TaskForce\Exceptions\DestinationFileException;
use TaskForce\Exceptions\SourceDirectoryException;
use TaskForce\Exceptions\SourceFileException;

/**
 * Конвертер файлов с данными в формате CSV в SQL.
 */
class CsvToSqlFilesConverter
{
    private string $sourceDir;
    private string $destinationDir;

    /**
     * Создаёт экземпляр класса CsvToSqlFilesConverter.
     *
     * @param string $sourceDir      Путь к папке с CSV файлами.
     * @param string $destinationDir Путь к папке для создания SQL файлов.
     */
    public function __construct(string $sourceDir, string $destinationDir)
    {
        $this->sourceDir = $sourceDir;
        $this->destinationDir = $destinationDir;
    }

    /**
     * Конвертирует файлы.
     *
     * @return void
     * @throws DestinationFileException Исключение при ошибке доступа к конечной папке.
     * @throws SourceDirectoryException Исключение при ошибке доступа к папке с CSV файлами.
     */
    public function convert(): void
    {
        if (!is_dir($this->sourceDir)) {
            throw new SourceDirectoryException(
                'Переданной директории не существует'
            );
        }

        try {
            $fileIterator = new FilesystemIterator($this->sourceDir);
        } catch (RuntimeException $exception) {
            throw new SourceDirectoryException(
                'Не удалось открыть переданную директорию'
                . $exception->getMessage()
            );
        }

        foreach ($fileIterator as $fileInfo) {
            $this->convertFile($fileInfo);
        }
    }

    /**
     * Конвертирует файл из CSV формата в SQL.
     *
     * @param SplFileInfo $fileInfo Объект с информацией о файле.
     *
     * @return void
     * @throws DestinationFileException Исключение при ошибке обращения к конечному файлу.
     * @throws SourceDirectoryException Исключение при ошибке обращения к файлу с CSV данными.
     */
    private function convertFile(SplFileInfo $fileInfo): void
    {
        $csvFileImporter = new CsvFileImporter($fileInfo->getPathname());

        try {
            $csvFileImporter->import();
        } catch (SourceFileException $exception) {
            throw new SourceDirectoryException($exception->getMessage());
        }

        $sqlFileCreator = new SqlFileCreator(
            $fileInfo->getBasename('.csv'),
            $this->destinationDir,
            $csvFileImporter->data,
            'taskforce'
        );

        try {
            $sqlFileCreator->create();
        } catch (DestinationFileException $exception) {
            throw new DestinationFileException($exception->getMessage());
        }
    }
}
