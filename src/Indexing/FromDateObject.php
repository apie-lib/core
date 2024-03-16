<?php
namespace Apie\Core\Indexing;

use Apie\Common\ContextConstants;
use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\StringList;
use DateTimeInterface;
use Stringable;

final class FromDateObject implements IndexingStrategyInterface
{
    private const DEFAULT_FORMATS = [
        DateTimeInterface::ATOM,
        'D',
        'l',
        'S',
        'W',
        'F',
        'M',
        'L',
        'a',
        'e',
        'T'
    ];

    public function support(object $object): bool
    {
        return $object instanceof DateTimeInterface;
    }

    /**
     * @return array<string, int>
     */
    public function getIndexes(object $class, ApieContext $context, Indexer $indexer): array
    {
        assert($class instanceof DateTimeInterface);
        $res = [];
        if ($class instanceof Stringable) {
            $res[$class->__toString()] = 5;
        }
        $formats = self::DEFAULT_FORMATS;
        if ($context->hasContext(ContextConstants::DATEFORMATS)) {
            $formats = new StringList($context->getContext(ContextConstants::DATEFORMATS));
        }
        foreach ($formats as $format) {
            $res[$class->format($format)] = 1;
        }
        return $res;
    }
}