<?php
namespace Apie\Tests\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\Strategy\ScalarStrategy;
use Apie\Core\ReflectionTypeFactory;
use PHPUnit\Framework\TestCase;

class ScalarStrategyTest extends TestCase
{
    /**
     * @test
     * @dataProvider scalarProvider
     */
    public function it_can_typehint_scalars(ScalarType $expectedScalar, string $typehint)
    {
        $context = new ApieContext();
        /** @var ScalarStrategy $actual */
        $actual = MetadataFactory::getMetadataStrategyForType(ReflectionTypeFactory::createReflectionType($typehint));
        $this->assertInstanceOf(ScalarStrategy::class, $actual);
        $metadata = $actual->getCreationMetadata($context);
        $this->assertSame($expectedScalar, $metadata->toScalarType());
        $metadata = $actual->getModificationMetadata($context);
        $this->assertSame($expectedScalar, $metadata->toScalarType());
        $metadata = $actual->getResultMetadata($context);
        $this->assertSame($expectedScalar, $metadata->toScalarType());
    }

    public function scalarProvider()
    {
        yield [ScalarType::ARRAY, 'array'];
        yield [ScalarType::BOOLEAN, 'bool'];
        yield [ScalarType::FLOAT, 'float'];
        yield [ScalarType::INTEGER, 'int'];
        yield [ScalarType::MIXED, 'mixed'];
        yield [ScalarType::NULL, 'null'];
        yield [ScalarType::BOOLEAN, 'false'];
        if (PHP_VERSION_ID >= 80200) {
            yield [ScalarType::BOOLEAN, 'true'];
        }
        yield [ScalarType::STDCLASS, 'stdClass'];
        yield [ScalarType::STRING, 'string'];
    }
}
