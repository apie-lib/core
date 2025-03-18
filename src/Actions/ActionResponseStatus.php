<?php
namespace Apie\Core\Actions;

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
}
