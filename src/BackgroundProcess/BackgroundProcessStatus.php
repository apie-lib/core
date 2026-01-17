<?php
namespace Apie\Core\BackgroundProcess;

use Apie\Core\Attributes\Description;

enum BackgroundProcessStatus: string
{
    #[Description('The background process has started and is running')]
    case Active = 'active';
    #[Description('The background process has finished successfully')]
    case Finished = 'finished';
    #[Description('The background process has finished with errors even after retries')]
    case TooManyErrors = 'tooManyErrors';
    #[Description('The background process has been canceled')]
    case Canceled = 'canceled';
}
