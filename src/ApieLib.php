<?php
namespace Apie\Core;

use Apie\Common\Other\Audit\AuditEvent;
use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Core\Permissions\PermissionInterface;
use Apie\Core\Permissions\SerializedPermission;
use Apie\Core\Translator\ValueObjects\AbstractTranslation;
use Apie\Core\Translator\ValueObjects\MenuHeader;
use Apie\Core\Translator\ValueObjects\ResourceAddResourceButtonLabel;
use Apie\Core\Translator\ValueObjects\ResourceAddResourceHeader;
use Apie\Core\Translator\ValueObjects\ResourceOverviewHeader;
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
     * @var array<class-string<object>, string|class-string<object>> $aliases
     */
    private static $aliases = [
        AbstractTranslation::class => MenuHeader::class . '|' . ResourceOverviewHeader::class . '|' . ResourceAddResourceButtonLabel::class . '|' . ResourceAddResourceHeader::class,
        PermissionInterface::class => SerializedPermission::class,
        AuditEvent::class => 'Apie\Common\Other\Audit\AuditCreate|Apie\Common\Other\Audit\AuditModified|Apie\Common\Other\Audit\AuditRemoved|Apie\Common\Other\Audit\AuditRead|Apie\Common\Other\Audit\AuditMethodCalled'
    ];

    public static function resetAliases(): void
    {
        $prop = new \ReflectionProperty(__CLASS__, 'aliases');
        $prop->setValue(null, $prop->getDefaultValue());
    }

    /**
     * @param class-string<object> $alias
     * @param string|class-string<object> $target
     * @return void
     */
    public static function registerAlias(string $alias, string $target): void
    {
        self::$aliases[$alias] = $target;
    }

    /**
     * @param class-string<object> $alias
     */
    public static function hasAlias(string $alias): bool
    {
        return isset(self::$aliases[$alias]);
    }

    /**
     * @param class-string<object> $alias
     * @return string|class-string<object>
     */
    public static function getAlias(string $alias): string
    {
        if (!isset(self::$aliases[$alias])) {
            throw new IndexNotFoundException($alias);
        }
        return self::$aliases[$alias];
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public const VERSION = '1.0.0-RC3';

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
