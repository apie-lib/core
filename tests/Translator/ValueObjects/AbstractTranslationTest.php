<?php
namespace Apie\Tests\Core\Translator\ValueObjects;

use Apie\Core\Translator\ValueObjects\AbstractTranslation;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AbstractTranslationTest extends TestCase
{
    use TestWithFaker;
    #[Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(AbstractTranslation::class);
    }

    #[Test]
    public function it_has_a_toString_method()
    {
        $testItem = DummyTranslation::fromNative('apie.mid-section.parent');
        $this->assertEquals('apie.mid-section.parent', $testItem->__toString());
        $this->assertEquals('apie.mid-section.parent', $testItem->jsonSerialize());
    }

    #[Test]
    #[DataProvider('simplificationsProvider')]
    public function it_can_provide_translation_simplifications(array $expected, string $input)
    {
        $testItem = DummyTranslation::fromNative($input);
        $this->assertEquals(
            $expected,
            json_decode(json_encode($testItem->getSimplifications()), true)
        );
    }

    public static function simplificationsProvider(): \Generator
    {
        yield 'no prefix, no suffix' => [
            [
            ],
            'apie.mid-section.parent'
        ];

        yield 'no prefix, one plural' => [
            [
                'apie.mid-section.parent'
            ],
            'apie.mid-section.parent.singular'
        ];

        yield 'no prefix, one plural, one authenticated' => [
            [
                'apie.mid-section.parent.authenticated',
                'apie.mid-section.parent.singular',
            ],
            'apie.mid-section.parent.singular.authenticated'
        ];

        yield 'no prefix, no plural, one authenticated' => [
            [
                'apie.mid-section.parent',
            ],
            'apie.mid-section.parent.authenticated'
        ];

        yield 'bounded context, no suffix' => [
            [
                'apie.mid-section.parent',
            ],
            'apie.bounded.test.mid-section.parent'
        ];

        yield 'bounded context, one plural' => [
            [
                'apie.mid-section.parent',
                'apie.bounded.test.mid-section.parent',
                'apie.mid-section.parent.singular',
            ],
            'apie.bounded.test.mid-section.parent.singular'
        ];

        yield 'bounded context, one plural, one authenticated' => [
            [
                'apie.mid-section.parent.authenticated',
                'apie.mid-section.parent.singular',
                'apie.bounded.test.mid-section.parent.authenticated',
                'apie.bounded.test.mid-section.parent.singular',
                'apie.mid-section.parent.singular.authenticated',
            ],
            'apie.bounded.test.mid-section.parent.singular.authenticated'
        ];
        
        yield 'bounded context, no plural, one authenticated' => [
            [
                'apie.mid-section.parent',
                'apie.bounded.test.mid-section.parent',
                'apie.mid-section.parent.authenticated',
            ],
            'apie.bounded.test.mid-section.parent.authenticated'
        ];

        yield 'bounded context, resource name, no suffix' => [
            [
                'apie.bounded.test.mid-section.parent',
                'apie.resource.test.mid-section.parent',
            ],
            'apie.bounded.test.resource.test.mid-section.parent'
        ];

        yield 'bounded context, resource name, one plural' => [
            [
                'apie.bounded.test.mid-section.parent',
                'apie.resource.test.mid-section.parent',
                'apie.bounded.test.resource.test.mid-section.parent',
                'apie.bounded.test.mid-section.parent.singular',
                'apie.resource.test.mid-section.parent.singular',
            ],
            'apie.bounded.test.resource.test.mid-section.parent.singular'
        ];

        yield 'bounded context, resource name, one plural, one authenticated' => [
            [
                'apie.bounded.test.mid-section.parent.authenticated',
                'apie.resource.test.mid-section.parent.authenticated',
                'apie.bounded.test.mid-section.parent.singular',
                'apie.resource.test.mid-section.parent.singular',
                'apie.bounded.test.resource.test.mid-section.parent.authenticated',
                'apie.bounded.test.mid-section.parent.singular.authenticated',
                'apie.bounded.test.resource.test.mid-section.parent.singular',
                'apie.resource.test.mid-section.parent.singular.authenticated',
            ],
            'apie.bounded.test.resource.test.mid-section.parent.singular.authenticated'
        ];
        
        yield 'bounded context, resource name, no plural, one authenticated' => [
            [
                'apie.bounded.test.mid-section.parent',
                'apie.resource.test.mid-section.parent',
                'apie.bounded.test.mid-section.parent.authenticated',
                'apie.bounded.test.resource.test.mid-section.parent',
                'apie.resource.test.mid-section.parent.authenticated'
            ],
            'apie.bounded.test.resource.test.mid-section.parent.authenticated'
        ];

        yield 'resource name, no suffix' => [
            [
                'apie.mid-section.parent',
            ],
            'apie.resource.test.mid-section.parent'
        ];

        yield 'resource name, one plural' => [
            [
                'apie.mid-section.parent',
                'apie.resource.test.mid-section.parent',
                'apie.mid-section.parent.singular',
            ],
            'apie.resource.test.mid-section.parent.singular'
        ];

        yield 'resource name, one plural, one authenticated' => [
            [
                'apie.mid-section.parent.authenticated',
                'apie.mid-section.parent.singular',
                'apie.resource.test.mid-section.parent.authenticated',
                'apie.mid-section.parent.singular.authenticated',
                'apie.resource.test.mid-section.parent.singular',
            ],
            'apie.resource.test.mid-section.parent.singular.authenticated'
        ];
        
        yield 'resource name, no plural, one authenticated' => [
            [
                'apie.mid-section.parent',
                'apie.mid-section.parent.authenticated',
                'apie.resource.test.mid-section.parent',
            ],
            'apie.resource.test.mid-section.parent.authenticated'
        ];
    }
}
