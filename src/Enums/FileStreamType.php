<?php
namespace Apie\Core\Enums;

enum FileStreamType: string
{
    /**
     * https://developer.mozilla.org/en-US/docs/Web/API/FileReader/readAsArrayBuffer
     */
    case ArrayBuffer = 'readAsArrayBuffer';

    /**
     * https://developer.mozilla.org/en-US/docs/Web/API/FileReader/readAsBinaryString
     *
     * I added for consistency, but it's marked as deprecated.
     */
    case BinaryString = 'readAsBinaryString';

    /**
     * https://developer.mozilla.org/en-US/docs/Web/API/FileReader/readAsDataURL
     */
    case Base64String = 'readAsDataURL';

    /**
     * https://developer.mozilla.org/en-US/docs/Web/API/FileReader/readAsText
     */
    case PlainText = 'readAsText';
}
