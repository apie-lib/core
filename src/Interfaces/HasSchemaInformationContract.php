<?php


namespace Apie\Core\Interfaces;

use Apie\OpenapiSchema\Contract\SchemaContract;

interface HasSchemaInformationContract
{
    public static function toSchema(): SchemaContract;
}