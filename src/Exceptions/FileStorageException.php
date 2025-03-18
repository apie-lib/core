<?php
namespace Apie\Core\Exceptions;

use Apie\TypeConverter\Exceptions\GetMultipleChainedExceptionInterface;
use Throwable;

final class FileStorageException extends ApieException implements HttpStatusCodeException, GetMultipleChainedExceptionInterface
{
    /**
     * @param array<int, Throwable> $exceptions
     */
    public function __construct(string $message, private array $exceptions)
    {
        parent::__construct(
            sprintf(
                "%s:\n%s",
                $message,
                implode(
                    ",\n",
                    array_map(
                        function (Throwable $exception) {
                            return $exception->getMessage();
                        },
                        $exceptions
                    )
                )
            ),
            0,
            empty($exceptions) ? null : reset($exceptions)
        );
    }

    public function getStatusCode(): int
    {
        return 503;
    }

    public function getChainedExceptions(): array
    {
        return $this->exceptions;
    }
}
