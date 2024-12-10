<?php
namespace Apie\Core;

use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\Utils;
use Apie\SchemaGenerator\Other\JsonSchemaFormatValidator;
use Beste\Clock\SystemClock;
use League\OpenAPIValidation\Schema\TypeFormats\FormatsContainer;
use Psr\Clock\ClockInterface;
use ReflectionClass;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

final class ApieLib
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public const VERSION = '1.0.0.x-dev';

    public const APIE_FORM_ELEMENTS = '0.7.0';

    public const APIE_STACKTRACE = '0.1.6';

    private static ClockInterface $clock;


    /**
     * Workaround for many integration tests in validating string value objects in OneOf or AllOf schema's.
     *
     * @param class-string<ValueObjectInterface> $class
     */
    public static function registerValueObject(string $class): void
    {
        if (class_exists(FormatsContainer::class) && class_exists(JsonSchemaFormatValidator::class)) {
            $format = strtolower(Utils::getDisplayNameForValueObject(new ReflectionClass($class)));
            if (!FormatsContainer::getFormat('string', $format)) {
                FormatsContainer::registerFormat('string', $format, new JsonSchemaFormatValidator($class));
            }
            if (!FormatsContainer::getFormat('number', $format)) {
                FormatsContainer::registerFormat('number', $format, new JsonSchemaFormatValidator($class));
            }
        }
    }

    public static function getPsrClock(): ClockInterface
    {
        if (!isset(self::$clock)) {
            self::$clock = SystemClock::create();
        }
        return self::$clock;
    }

    public static function setPsrClock(ClockInterface $clock): void
    {
        self::$clock = $clock;
    }

    public static function dumpValueException(mixed $input): never
    {
        $cloner = new VarCloner();
        $dumper = new CliDumper();
        $output = fopen('php://memory', 'r+b');

        $dumper->dump($cloner->cloneVar($input), $output);
        throw new \LogicException(stream_get_contents($output, -1, 0));
    }
}
