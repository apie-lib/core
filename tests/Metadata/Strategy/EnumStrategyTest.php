<?php
namespace Apie\Tests\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\EnumMetadata;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\Strategy\EnumStrategy;
use Apie\Fixtures\Enums\ColorEnum;
use Apie\Fixtures\Enums\EmptyEnum;
use Apie\Fixtures\Enums\IntEnum;
use Apie\Fixtures\Enums\NoValueEnum;
use Apie\Fixtures\Enums\RestrictedEnum;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class EnumStrategyTest extends TestCase
{
    /**
     * @test
     * @dataProvider enumOptionsProvider
     */
    public function it_can_extract_values(array $expectedOptions, ScalarType $expectedScalar, ApieContext $context, string $class)
    {
        /** @var EnumStrategy $actual */
        $actual = MetadataFactory::getMetadataStrategy(new ReflectionClass($class));
        $this->assertInstanceOf(EnumStrategy::class, $actual);
        $metadata = $actual->getCreationMetadata($context);
        $this->assertSame($expectedScalar, $metadata->toScalarType());
        $this->assertEquals($expectedOptions, $metadata->getOptions($context, true));
    }

    public function enumOptionsProvider()
    {
        $context = new ApieContext();
        yield [
            [],
            ScalarType::STRING,
            $context,
            EmptyEnum::class
        ];
        yield [
            [
                'RED' => 'red',
                'GREEN' => 'green',
                'BLUE' => 'blue',
            ],
            ScalarType::STRING,
            $context,
            ColorEnum::class
        ];
        yield [
            [
                'RED' => 0,
                'GREEN' => 1,
                'BLUE' => 2,
            ],
            ScalarType::INTEGER,
            $context,
            IntEnum::class,
        ];
        yield [
            [
                'RED' => 'RED',
                'GREEN' => 'GREEN',
                'BLUE' => 'BLUE',
            ],
            ScalarType::STRING,
            $context,
            NoValueEnum::class
        ];
        yield [
            [
            ],
            ScalarType::STRING,
            $context,
            RestrictedEnum::class,
        ];
        yield [
            [
                'RED' => 'red',
            ],
            ScalarType::STRING,
            new ApieContext(['locale' => 'nl']),
            RestrictedEnum::class,
        ];
        yield [
            [
                'RED' => 'red',
                'GREEN' => 'green',
                'BLUE' => 'blue',
                'ORANGE' => 'orange',
            ],
            ScalarType::STRING,
            new ApieContext(['locale' => 'nl', 'authenticated' => true]),
            RestrictedEnum::class,
        ];
        yield [
            [
                'GREEN' => 'green',
            ],
            ScalarType::STRING,
            new ApieContext(['authenticated' => true]),
            RestrictedEnum::class,
        ];
    }
}
