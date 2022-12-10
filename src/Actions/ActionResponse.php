<?php
namespace Apie\Core\Actions;

use Apie\Core\Context\ApieContext;
use Apie\Core\Exceptions\ClientRequestException;
use Apie\Core\Exceptions\HttpStatusCodeException;
use Throwable;

final class ActionResponse
{
    public readonly mixed $result;

    public readonly mixed $resource;
    
    public readonly Throwable&HttpStatusCodeException $error;

    private mixed $nativeData;

    private function __construct(private readonly ApieFacadeInterface $apieFacade, public readonly ApieContext $apieContext, public readonly ActionResponseStatus $status)
    {
    }

    public static function createClientError(ApieFacadeInterface $apieFacade, ApieContext $apieContext, Throwable $error): self
    {
        $res = new self($apieFacade, $apieContext, ActionResponseStatus::CLIENT_ERROR);
        $statusCode = ($error instanceof HttpStatusCodeException) ? $error->getStatusCode() : 500;
        if ($statusCode < 400 || $statusCode >= 500) {
            $error = new ClientRequestException($error);
        }
        $res->result = $error;
        /** @var Throwable&HttpStatusCodeException $error */
        $res->error = $error;
        return $res;
    }

    public static function createCreationSuccess(ApieFacadeInterface $apieFacade, ApieContext $apieContext, mixed $result, mixed $resource): self
    {
        $res = new self($apieFacade, $apieContext, ActionResponseStatus::CREATED);
        $res->result = $result;
        $res->resource = $resource;
        return $res;
    }

    public static function createRunSuccess(ApieFacadeInterface $apieFacade, ApieContext $apieContext, mixed $result, mixed $resource): self
    {
        $res = new self($apieFacade, $apieContext, ActionResponseStatus::SUCCESS);
        $res->result = $result;
        $res->resource = $resource;
        return $res;
    }

    /**
     * Returns HTTP status code that should be returned if you create a response.
     */
    public function getStatusCode(): int
    {
        return match ($this->status) {
            ActionResponseStatus::CLIENT_ERROR => $this->error->getStatusCode(),
            ActionResponseStatus::CREATED => 201,
            ActionResponseStatus::SUCCESS => 200,
            ActionResponseStatus::DELETED => 204,
            ActionResponseStatus::NOT_FOUND => 404,
            ActionResponseStatus::PERISTENCE_ERROR => 409,
            default => 500,
        };
    }

    public function getResultAsNativeData(): mixed
    {
        if (!isset($this->nativeData)) {
            $this->nativeData = $this->apieFacade->normalize($this->result, $this->apieContext);
        }
        return $this->nativeData;
    }
}
