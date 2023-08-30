<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\Core\Dto\DtoInterface;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Metadata\Fields\FieldInterface;
use Apie\Core\Metadata\GetterInterface;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;
use ReflectionNamedType;

class FromGetters implements IndexingStrategyInterface
{
    public function support(object $object): bool
    {
        return $object instanceof EntityInterface || $object instanceof DtoInterface;
    }

    /**
     * @return array<string, int>
     */
    public function getIndexes(object $object, ApieContext $context, Indexer $indexer): array
    {
        $metadata = MetadataFactory::getResultMetadata(new ReflectionClass($object), $context);
        $result = [];
        foreach ($metadata->getHashmap() as $propertyName => $fieldMetadata) {
            if (!$fieldMetadata->isField() || !$fieldMetadata instanceof GetterInterface) {
                continue;
            }
            /** @var GetterInterface&FieldInterface $fieldMetadata */
            $typehint = $fieldMetadata->getTypehint();
            if (!$typehint instanceof ReflectionNamedType) {
                continue;
            }
            $value = $fieldMetadata->getValue($object, $context);
            if (is_object($value)) {
                $embeddedObjectResult = $indexer->getIndexesForObject($value, $context, $indexer);
                $result = Indexer::merge($result, $embeddedObjectResult);
            } elseif (is_string($value) || is_numeric($value)) {
                $value = (string) $value;
                $result[$value] = $this->getPrio($propertyName, $typehint, $fieldMetadata, $value, $result[$value] ?? 0);
            }
        }
        return $result;
    }

    private function getPrio(
        string $propertyName,
        ReflectionNamedType $typehint,
        FieldInterface&GetterInterface $fieldMetadata,
        string $value,
        int $currentPrio
    ): int {
        if (in_array($typehint->getName(), ['string', 'int', 'float'])) {
            return max(
                match ($propertyName) {
                    'id' => 1,
                    'name' => 5,
                    'description' => 4,
                    default => 2,
                },
                $currentPrio
            );
        } elseif (!$typehint->isBuiltin() && class_exists($typehint->getName())) {
            $refl = new ReflectionClass($typehint->getName());
            if ($refl->implementsInterface(ValueObjectInterface::class)) {
                $returnType = $refl->getMethod('toNative')->getReturnType();
                assert($returnType instanceof ReflectionNamedType);
                return $this->getPrio(
                    $propertyName,
                    $returnType,
                    $fieldMetadata,
                    $value,
                    $currentPrio
                );
            }
        }
        return 0;
    }
}
