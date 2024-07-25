<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\Core\FileStorage\StoredFile;
use Psr\Http\Message\UploadedFileInterface;

class FromUploadedFile implements IndexingStrategyInterface
{
    public function support(object $object): bool
    {
        return $object instanceof UploadedFileInterface;
    }

    /**
     * @param UploadedFileInterface $object
     * @return array<string, int>
     */
    public function getIndexes(object $object, ApieContext $context, Indexer $indexer): array
    {
        $object = StoredFile::createFromUploadedFile($object);
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
        return $index;
    }
}
