<?php
namespace Apie\Core\Actions;

use Apie\Core\Exceptions\ActionNotAllowedException;
use Apie\Core\Exceptions\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Exception;

enum ActionResponseStatus: string
{
    /**
     * Resource was properly created.
     */
    case CREATED = 'created';
    /**
     * Action was properly executed.
     */
    case SUCCESS = 'success';

    /**
     * Resource was properly deleted
     */
    case DELETED = 'deleted';

    /**
     * Permission denied or authorization required error
     */
    case AUTHORIZATION_ERROR = 'authorization_error';

    /**
     * There is something wrong with the input.
     * For example if it is an API CALL the request body has invalid data.
     */
    case CLIENT_ERROR = 'client_error';

    /**
     * {id} placeholder in route could not be found.
     */
    case NOT_FOUND = 'not_found';

    /**
     * There is something wrong with storing the entity.
     *
     * For example: database error, unique constraints, etc.
     */
    case PERISTENCE_ERROR = 'persistence_error';

    /**
     * There is something wrong with displaying the result of the action.
     */
    case OUTPUT_ERROR = 'output_error';

    /**
     * Any other error is considered a server error.
     */
    case SERVER_ERROR = 'server_error';

    public static function createFromError(\Throwable $error): self
    {
        if ($error instanceof ActionNotAllowedException) {
            return self::AUTHORIZATION_ERROR;
        }
        if (!($error instanceof Exception)) {
            return self::SERVER_ERROR;
        }
        if ($error instanceof EntityNotFoundException) {
            return self::NOT_FOUND;
        }
        if ($error instanceof ORMException) {
            return self::PERISTENCE_ERROR;
        }
        return self::CLIENT_ERROR;
    }
}
