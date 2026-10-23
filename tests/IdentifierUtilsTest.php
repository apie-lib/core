<?php
namespace Apie\Tests\Core;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\IdentifierUtils;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class IdentifierUtilsTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_skip_reflection_errors_when_requested(): void
    {
        $context = new ApieContext([
            ContextConstants::RESOURCE_NAME => 'missing\\ResourceName',
        ]);

        $this->assertNull(IdentifierUtils::idStringToIdentifier('42', $context, false));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_reflection_errors_by_default(): void
    {
        $context = new ApieContext([
            ContextConstants::RESOURCE_NAME => 'missing\\ResourceName',
        ]);

        $this->expectException(ReflectionException::class);
        IdentifierUtils::idStringToIdentifier('42', $context);
    }
}
