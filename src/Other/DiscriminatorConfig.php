<?php
namespace Apie\Core\Other;

use Apie\Core\Attributes\SchemaMethod;

#[SchemaMethod('provideSchema')]
class DiscriminatorConfig
{
    public function __construct(private string $discriminator, private string $className)
    {
    }

    public function getDiscriminator(): string
    {
        return $this->discriminator;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function provideSchema()
    {
        return [
            'type' => 'object',
            'additionalProperties' => true,
        ];
    }
}
