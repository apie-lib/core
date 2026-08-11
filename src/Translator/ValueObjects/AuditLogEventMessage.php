<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Identifiers\KebabCaseSlug;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;

final class AuditLogEventMessage extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'audit_log\.(unknown_event|migration|created|replaced|method_called\.:method|migration|modified|read_list|read|removed)';

    public function getFallbackText(): string
    {
        if (preg_match('/^audit_log\.method_called\.(?<method>[^.]+)($|\.)/', $this->middleSection, $matches)) {
            return 'Called method ' . $matches['method'];
        }
        if (preg_match('/^audit_log\.(?<message>[^.]+)($|\.)/', $this->middleSection, $matches)) {
            return ucfirst(SnakeCaseSlug::fromText($matches['message']));
        }
        return 'Unknown event';
    }

    public static function createUnknownEvent(ApieContext $context): static
    {
        return new AuditLogEventMessage(
            TranslationStringPrefix::fromApieContext($context),
            'audit_log.unknown_event',
            new TranslationStringSuffix(),
            new ItemHashmap()
        );
    }

    public static function createMigrationEvent(ApieContext $context): static
    {
        return new AuditLogEventMessage(
            TranslationStringPrefix::fromApieContext($context),
            'audit_log.migration',
            new TranslationStringSuffix(),
            new ItemHashmap()
        );
    }

    public static function createResourceCreatedEvent(ApieContext $context): static
    {
        return new AuditLogEventMessage(
            TranslationStringPrefix::fromApieContext($context),
            'audit_log.created',
            new TranslationStringSuffix(),
            new ItemHashmap()
        );
    }

    public static function createResourceReplacedEvent(ApieContext $context): static
    {
        return new AuditLogEventMessage(
            TranslationStringPrefix::fromApieContext($context),
            'audit_log.replaced',
            new TranslationStringSuffix(),
            new ItemHashmap()
        );
    }

    public static function createResourceMethodCalledEvent(ApieContext $context, ?string $methodName): static
    {
        $methodName ??= '(unknown)';
        if ($context->hasContext(ContextConstants::METHOD_NAME)) {
            $methodName = KebabCaseSlug::fromText($context->getContext(ContextConstants::METHOD_NAME))->toNative();
        }
        return new AuditLogEventMessage(
            TranslationStringPrefix::fromApieContext($context),
            'audit_log.method_called.' . $methodName,
            new TranslationStringSuffix(),
            new ItemHashmap(['method' => $methodName])
        );
    }

    public static function createResourceRemovedEvent(ApieContext $context): static
    {
        return new AuditLogEventMessage(
            TranslationStringPrefix::fromApieContext($context),
            'audit_log.removed',
            new TranslationStringSuffix(),
            new ItemHashmap()
        );
    }

    public static function createResourceModifiedEvent(ApieContext $context): static
    {
        return new AuditLogEventMessage(
            TranslationStringPrefix::fromApieContext($context),
            'audit_log.modified',
            new TranslationStringSuffix(),
            new ItemHashmap()
        );
    }

    public static function createResourceRetrievedEvent(ApieContext $context): static
    {
        return new AuditLogEventMessage(
            TranslationStringPrefix::fromApieContext($context),
            'audit_log.read',
            new TranslationStringSuffix(),
            new ItemHashmap()
        );
    }

    public static function createResourceRetrievedInListEvent(ApieContext $context): static
    {
        return new AuditLogEventMessage(
            TranslationStringPrefix::fromApieContext($context),
            'audit_log.read_list',
            new TranslationStringSuffix(),
            new ItemHashmap()
        );
    }
}
