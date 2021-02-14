<?php
namespace Apie\Core\Exceptions;

/**
 * Exception thrown when the page index filled in for pagination is negative.
 */
class PageIndexShouldNotBeNegativeException extends ApieException implements LocalizationableException
{
    public function __construct()
    {
        parent::__construct(422, 'Page index should not be negative!');
    }

    public function getI18n(): LocalizationInfo
    {
        return new LocalizationInfo(
            'validation.min',
            [
                'value' => 'page',
                'minimum' => 0
            ]
        );
    }
}
