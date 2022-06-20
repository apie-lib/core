<?php
namespace Apie\Core\Other;

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
}
