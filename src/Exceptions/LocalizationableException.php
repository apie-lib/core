<?php


namespace Apie\Core\Exceptions;


interface LocalizationableException
{
    public function getI18n(): LocalizationInfo;
}