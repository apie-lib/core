<?php
namespace Apie\Core\Other;

use RuntimeException;

final class ActualFileWriter implements FileWriterInterface
{
    public function writeFile(string $filename, string $fileContents)
    {
        if (false === @file_put_contents($filename, $fileContents)) {
            throw new RuntimeException('I can not write to file: ' . $filename);
        }
    }
}
