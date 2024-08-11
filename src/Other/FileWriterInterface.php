<?php
namespace Apie\Core\Other;

interface FileWriterInterface
{
    public function clearPath(string $path): void;
    public function writeFile(string $filename, string $fileContents): void;
}
