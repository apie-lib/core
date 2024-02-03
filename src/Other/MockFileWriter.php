<?php
namespace Apie\Core\Other;

final class MockFileWriter implements FileWriterInterface
{
    public array $writtenFiles = [];

    public function writeFile(string $filename, string $fileContents)
    {
        $this->writtenFiles[$filename] = $fileContents;
    }
}
