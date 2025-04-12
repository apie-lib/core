<?php
namespace Apie\Core\BackgroundProcess;

enum BackgroundProcessStatus: string
{
    case Active = 'active';
    case Finished = 'finished';
    case TooManyErrors = 'tooManyErrors';
    case Canceled = 'canceled';
}
