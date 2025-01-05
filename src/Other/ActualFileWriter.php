<?php
namespace Apie\Core\Other;

use RuntimeException;

final class ActualFileWriter implements FileWriterInterface, FileReaderInterface
{
    public function writeFile(string $filename, string $fileContents): void
    {
        @mkdir(dirname($filename), recursive: true);
        if ($fileContents === @file_get_contents($filename)) {
            return;
        }
        if (false === @file_put_contents($filename, $fileContents)) {
            throw new RuntimeException('I can not write to file: ' . $filename);
        }
    }

    public function clearPath(string $path): void
    {
        if ($path === '/') {
            throw new \LogicException('I will not remove everything');
        }
        if (is_dir($path)) {
            system('rm -rf ' . escapeshellarg($path));
        }
        @mkdir($path, recursive: true);
    }

    public function fileExists(string $filePath): bool
    {
        return file_exists($filePath) && !is_dir($filePath);
    }

    public function readContents(string $filePath): string
    {
        $contents = @file_get_contents($filePath);
        if ($contents === false) {
            throw new \RuntimeException('Could not read ' . $filePath);
        }
        return $contents;
    }
}
