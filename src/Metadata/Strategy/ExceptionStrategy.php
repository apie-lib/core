<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Exceptions\HttpStatusCodeException;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\GetterMethod;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Serializer\Exceptions\ValidationException;
use ReflectionClass;
use Throwable;

final class ExceptionStrategy implements StrategyInterface
{
    private RegularObjectStrategy $regular;

    public static function supports(ReflectionClass $class): bool
    {
        return is_a($class->name, Throwable::class, true);
    }

    /**
     * @param ReflectionClass<Throwable> $class
     */
    public function __construct(private ReflectionClass $class)
    {
        $this->regular = new RegularObjectStrategy($class);
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        return $this->regular->getCreationMetadata($context);
    }

    public function getModificationMetadata(ApieContext $context): CompositeMetadata
    {
        return $this->regular->getModificationMetadata($context);
    }

    public function getResultMetadata(ApieContext $context): CompositeMetadata
    {
        $fields = [
            'message' => new GetterMethod($this->class->getMethod('getMessage')),
            'code' => new GetterMethod($this->class->getMethod('getCode')),
        ];
        if ($this->class->implementsInterface(HttpStatusCodeException::class)) {
            $fields['statusCode'] = new GetterMethod($this->class->getMethod('getStatusCode'));
        }
        if ($this->class->name === ValidationException::class) {
            $fields['errors'] = new GetterMethod($this->class->getMethod('getErrors'));
        }
        return new CompositeMetadata(
            new MetadataFieldHashmap($fields)
        );
    }
}
