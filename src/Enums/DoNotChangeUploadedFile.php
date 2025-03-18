<?php
namespace Apie\Core\Enums;

/**
 * Special enum used for file uploads in edit resource actions to tell the file upload will not be changed.
 */
enum DoNotChangeUploadedFile: string
{
    case DoNotChange = 'DoNotChange';
}
