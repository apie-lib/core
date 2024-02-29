<?php
namespace Apie\Core\Other;

final class MockFileWriter implements FileWriterInterface
{
    /**
     * @var array<string, string>
     */
    public array $writtenFiles = [];

    public function writeFile(string $filename, string $fileContents): void
    {
        $this->writtenFiles[$filename] = $fileContents;
    }
}
