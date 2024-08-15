<?php
namespace Apie\Core\Other;

final class MockFileWriter implements FileWriterInterface, FileReaderInterface
{
    /**
     * @var array<string, string>
     */
    public array $writtenFiles = [];

    public function writeFile(string $filename, string $fileContents): void
    {
        $this->writtenFiles[$filename] = $fileContents;
    }

    public function clearPath(string $path): void
    {
        $this->writtenFiles = array_filter(
            $this->writtenFiles,
            function (string $fileContents, string $fileName) use ($path) {
                return !str_starts_with($fileName, $path . '/');
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    public function fileExists(string $filePath): bool
    {
        return array_key_exists($filePath, $this->writtenFiles);
    }
    public function readContents(string $filePath): string
    {
        return $this->writtenFiles[$filePath];
    }
}
