<?php
namespace Apie\Core\Other;

use RuntimeException;

final class ActualFileWriter implements FileWriterInterface
{
    public function writeFile(string $filename, string $fileContents): void
    {
        @mkdir(dirname($filename), recursive: true);
        if (false === @file_put_contents($filename, $fileContents)) {
            throw new RuntimeException('I can not write to file: ' . $filename);
        }
    }

    public function clearPath(string $path): void
    {
        if (is_dir($path)) {
            system('rm -rf ' . escapeshellarg($path));
        }
        if ($path === '/') {
            throw new \LogicException('I will not remove everything');
        }
        @mkdir($path, recursive: true);
    }
}
