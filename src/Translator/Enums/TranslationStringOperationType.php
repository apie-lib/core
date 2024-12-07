<?php
namespace Apie\Core\Translator\Enums;

enum TranslationStringOperationType: string
{
    case Create = 'create';
    case Read = 'read';
    case List = 'list';
    case Update = 'update';
    case Delete = 'delete';

    /**
     * @return array<int, string>
     */
    public static function stringCases(): array
    {
        $cases = array_map(function (self $case) {
            return $case->value;
        }, self::cases());
        $cases[] = 'general';
        
        return $cases;
    }

    public function canRead(): bool
    {
        return $this === self::Read || $this === self::List;
    }

    public function canCreate(): bool
    {
        return $this === self::Create;
    }

    public function canUpdate(): bool
    {
        return $this === self::Update;
    }
}