<?php

namespace Apie\Core\Exceptions;

/**
 * Exception thrown when a page limit for the pagination is not a valid value.
 */
class InvalidPageLimitException extends ApieException implements LocalizationableException
{
    public function __construct()
    {
        parent::__construct(422, 'Page limit should not be lower than 1!');
    }

    public function getI18n(): LocalizationInfo
    {
        return new LocalizationInfo(
            'validation.min',
            [
                'value' => 'limit',
                'minimum' => 1
            ]
        );
    }
}
