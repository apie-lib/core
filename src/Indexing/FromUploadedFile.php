<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\CountWords\WordCounter;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FromUploadedFile implements IndexingStrategyInterface
{
    public function support(object $object): bool
    {
        return $object instanceof UploadedFileInterface
            || $object instanceof UploadedFile;
    }

    /**
     * @param UploadedFileInterface|UploadedFile $object
     * @return array<string, int>
     */
    public function getIndexes(object $object, ApieContext $context, Indexer $indexer): array
    {
        if ($object instanceof UploadedFileInterface) {
            return WordCounter::countFromResource(
                $object->getStream()->detach(),
                [],
                $object->getClientMediaType(),
                pathinfo($object->getClientFilename(), PATHINFO_EXTENSION)
            );
        }
        return WordCounter::countFromFile(
            $object->getPath(),
            [],
            $object->getMimeType()
        );
    }
}
