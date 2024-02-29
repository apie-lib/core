<?php
namespace Apie\Core\Other;

interface FileWriterInterface
{
    public function writeFile(string $filename, string $fileContents): void;
}
