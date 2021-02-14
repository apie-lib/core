<?php

namespace Apie\Core\PluginInterfaces;

use Apie\OpenapiSchema\Contract\SchemaContract;

interface SchemaProviderInterface
{
    /**
     * @return SchemaContract[]
     */
    public function getDefinedStaticData(): array;

    /**
     * @return DynamicSchemaInterface[]
     */
    public function getDynamicSchemaLogic(): array;
}
