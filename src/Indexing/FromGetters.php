<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
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
        return $object instanceof EntityInterface;
    }

    /**
     * @return array<string, int>
     */
    public function getIndexes(object $object, ApieContext $context): array
    {
        $metadata = MetadataFactory::getResultMetadata(new ReflectionClass($object), $context);
        $result = [];
        foreach ($metadata->getHashmap() as $propertyName => $fieldMetadata) {
            if (!$fieldMetadata->isField() || !$fieldMetadata instanceof GetterInterface) {
                continue;
            }
            $typehint = $fieldMetadata->getTypehint();
            if (!$typehint instanceof ReflectionNamedType) {
                continue;
            }
            $value = $fieldMetadata->getValue($object, $context);
            if ($value instanceof ValueObjectInterface) {
                $value = $value->toNative();
            }
            if ($value === null || is_array($value) || is_object($value)) {
                continue;
            }
            $value = (string) $value;
            $result[$value] = $this->getPrio($propertyName, $typehint, $fieldMetadata, $value, $result[$value] ?? 0);
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
                return $this->getPrio(
                    $propertyName,
                    $refl->getMethod('toNative')->getReturnType(),
                    $fieldMetadata,
                    $value,
                    $currentPrio
                );
            }
        }
        return 0;
    }
}
