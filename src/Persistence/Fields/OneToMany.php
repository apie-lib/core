<?php
namespace Apie\Core\Persistence\Fields;

class OneToMany extends IgnoredField
{
    public function __construct(
        string $declaredClass,
        string $propertyName,
        private readonly string $tableName
    ) {
        parent::__construct($declaredClass, $propertyName);
    }

    public function getTableReference(): string
    {
        return $this->tableName;
    }
}
