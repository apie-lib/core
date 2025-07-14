<?php
declare(strict_types=1);

namespace Apie\Tests\Core\BackgroundProcess;

use Apie\Core\ApieLib;
use Apie\Core\BackgroundProcess\BackgroundProcessDeclaration;
use Apie\Core\BackgroundProcess\BackgroundProcessStatus;
use Apie\Core\BackgroundProcess\SequentialBackgroundProcess;
use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemHashmap;
use Beste\Clock\FrozenClock;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class SequentialBackgroundProcessTest extends TestCase
{
    private ClockInterface $mockClock;
    private DateTimeImmutable $fixedTime;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixedTime = new DateTimeImmutable('2025-07-12 12:00:00');
        $this->mockClock = FrozenClock::at($this->fixedTime);
        ApieLib::setPsrClock($this->mockClock);
    }

    /**
     * SequentialBackgroundProcess needs an actual class to work here, so no mocks or anonymous classes.
     * To ease making tests we use this hacky method to create
     * a working class.
     */
    private function createNamedDeclaration(
        string $className,
        BackgroundProcessDeclaration $inner
    ): BackgroundProcessDeclaration {
        $fqcn = "\\Tests\\Helpers\\$className";
        if (!class_exists($fqcn)) {
            $innerClass = get_class($inner);

            eval("
                namespace Tests\\Helpers;

                final class $className implements \\Apie\\Core\\BackgroundProcess\\BackgroundProcessDeclaration {
                    private const INNER_CLASS = " . var_export($innerClass, true) . ";

                    private \$inner;

                    public function __construct()
                    {
                        \$this->inner = (new \ReflectionClass(self::INNER_CLASS))->newInstanceWithoutConstructor();
                    }

                    public function getCurrentVersion(): int
                    {
                        return \$this->inner->getCurrentVersion();
                    }

                    public static function getMaxRetries(int \$version): int
                    {
                        return self::INNER_CLASS::getMaxRetries(\$version);
                    }

                    public static function retrieveDeclaration(int \$version): array
                    {
                        return self::INNER_CLASS::retrieveDeclaration(\$version);
                    }
                }
            ");
        }

        return new $fqcn();
    }

    #[Test]
    public function runStep_executes_step_and_transitions_state(): void
    {
        $declaration = new class implements BackgroundProcessDeclaration {
            public static function getMaxRetries(int $version): int
            {
                return 1;
            }

            public static function retrieveDeclaration(int $version): array
            {
                return [
                    fn (ApieContext $ctx, ItemHashmap $payload) => 'step1result',
                    fn (ApieContext $ctx, ItemHashmap $payload) => 'step2result',
                ];
            }

            public function getCurrentVersion(): int
            {
                return 1;
            }
        };
        $declaration = $this->createNamedDeclaration('RunStepTest', $declaration);

        $payload = new ItemHashmap();
        $process = new SequentialBackgroundProcess($declaration, $payload);
        $apieContext = new ApieContext();

        // Act: run first step
        $process->runStep($apieContext);

        // Assert: first step executed
        $this->assertEquals(1, $process->getStep());
        $this->assertEquals('step1result', $process->getResult());
        $this->assertEquals(BackgroundProcessStatus::Active, $process->getStatus());

        // Act: run second step
        $process->runStep($apieContext);

        // Assert: second step executed and process finished
        $this->assertEquals(2, $process->getStep());
        $this->assertEquals('step2result', $process->getResult());
        $this->assertEquals(BackgroundProcessStatus::Active, $process->getStatus());
        $this->assertNull($process->getCompletionTime());

        // Act: finalize background
        $process->runStep($apieContext);
        $this->assertEquals(BackgroundProcessStatus::Finished, $process->getStatus());
        $this->assertEquals($this->fixedTime, $process->getCompletionTime());
    }

    #[Test]
    public function cancel_method_changes_status(): void
    {
        $declaration = new class implements BackgroundProcessDeclaration {
            public static function getMaxRetries(int $version): int
            {
                return 1;
            }

            public static function retrieveDeclaration(int $version): array
            {
                return [];
            }

            public function getCurrentVersion(): int
            {
                return 1;
            }
        };
        $declaration = $this->createNamedDeclaration('CancelChangesTest', $declaration);

        $payload = new ItemHashmap();
        $process = new SequentialBackgroundProcess($declaration, $payload);

        $this->assertEquals(BackgroundProcessStatus::Active, $process->getStatus());

        $process->cancel();
        $this->assertEquals(BackgroundProcessStatus::Canceled, $process->getStatus());
    }

    #[Test]
    public function runStep_handles_exception_and_retries(): void
    {
        $declaration = new class implements BackgroundProcessDeclaration {
            public static function getMaxRetries(int $version): int
            {
                return 2;
            }

            public static function retrieveDeclaration(int $version): array
            {
                return [
                    fn (ApieContext $ctx, ItemHashmap $payload) => throw new \RuntimeException('Step failed!'),
                ];
            }

            public function getCurrentVersion(): int
            {
                return 1;
            }
        };
        $declaration = $this->createNamedDeclaration('ExceptionTest', $declaration);

        $payload = new ItemHashmap();
        $process = new SequentialBackgroundProcess($declaration, $payload);
        $apieContext = new ApieContext();

        // First failure
        $process->runStep($apieContext);
        $this->assertEquals(BackgroundProcessStatus::Active, $process->getStatus());
        $this->assertCount(1, $process->getErrors());
        $this->assertEquals(1, $process->getRetries());

        // Second failure triggers too many errors
        $process->runStep($apieContext);
        $this->assertEquals(BackgroundProcessStatus::TooManyErrors, $process->getStatus());
        $this->assertCount(2, $process->getErrors());
    }

    #[Test]
    public function provideAllowedMethods_shows_list_of_available_methods(): void
    {
        $declaration = new class implements BackgroundProcessDeclaration {
            public static function getMaxRetries(int $version): int
            {
                return 1;
            }

            public static function retrieveDeclaration(int $version): array
            {
                return [];
            }

            public function getCurrentVersion(): int
            {
                return 1;
            }
        };
        $declaration = $this->createNamedDeclaration('AllowedMethodsTest', $declaration);

        $payload = new ItemHashmap();
        $process = new SequentialBackgroundProcess($declaration, $payload);

        $this->assertEquals(['cancel', 'runStep'], $process->provideAllowedMethods()->toArray());

        $process->cancel();

        $this->assertEquals([], $process->provideAllowedMethods()->toArray());
    }
}
