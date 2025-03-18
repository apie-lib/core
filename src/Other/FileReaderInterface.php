<?php
namespace Apie\Core\Other;

interface FileReaderInterface
{
    public function fileExists(string $filePath): bool;
    public function readContents(string $filePath): string;
}
