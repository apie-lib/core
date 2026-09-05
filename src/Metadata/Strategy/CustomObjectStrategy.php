<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\CustomObjectsMetadata;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Core\Metadata\StrategyInterface;
use BcMath\Number;
use DOMAttr;
use DOMElement;
use FFI\CData;
use FFI\CType;
use GMP;
use ReflectionClass;
use SimpleXMLElement;
use StreamBucket;
use Uri\Rfc3986\Uri;

class CustomObjectStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return in_array($class->name, [
            Uri::class,
            GMP::class,
            Number::class,
            StreamBucket::class,
            CType::class,
            CData::class,
            SimpleXMLElement::class,
            DOMAttr::class,
            DOMElement::class,
        ]);
    }

    /**
     * @param ReflectionClass<covariant object> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): MetadataInterface
    {
        return new CustomObjectsMetadata($this->class);
    }

    public function getModificationMetadata(ApieContext $context): MetadataInterface
    {
        return new CustomObjectsMetadata($this->class);
    }

    public function getResultMetadata(ApieContext $context): MetadataInterface
    {
        return new CustomObjectsMetadata($this->class);
    }
}
