<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\Core\FileStorage\StoredFile;
use Psr\Http\Message\UploadedFileInterface;
use WeakMap;

class FromUploadedFile implements IndexingStrategyInterface
{
    /**
     * @var WeakMap<UploadedFileInterface, array<string, int>>
     */
    private WeakMap $indexesCalculated;
    public function __construct()
    {
        $this->indexesCalculated = new WeakMap();
    }
    public function support(object $object): bool
    {
        return $object instanceof UploadedFileInterface;
    }

    /**
     * @param UploadedFileInterface $input
     * @return array<string, int>
     */
    public function getIndexes(object $input, ApieContext $context, Indexer $indexer): array
    {
        if (isset($this->indexesCalculated[$input])) {
            return $this->indexesCalculated[$input];
        }
        $object = StoredFile::createFromUploadedFile($input);
        $index = $object->getIndexing();
        $filename = $object->getClientFilename();
        if ($filename) {
            $index[$filename] = ($index[$filename] ?? 0) + 1;
        }
        $mime = $object->getClientMediaType();
        if ($mime) {
            $index[$mime] = ($index[$mime] ?? 0) + 1;
        }
        $server = $object->getServerMimeType();
        $index[$server] = ($index[$server] ?? 0) + 1;
        $this->indexesCalculated[$input] = $index;
        return $index;
    }
}
