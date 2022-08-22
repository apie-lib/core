<?php
namespace Apie\Core\Actions;

use Apie\Common\ApieFacade;
use Apie\Core\Context\ApieContext;
use Throwable;

final class ActionResponse
{
    public readonly mixed $result;

    public readonly mixed $resource;
    
    public readonly Throwable $error;

    private mixed $nativeData;

    private function __construct(private readonly ApieFacade $apieFacade, public readonly ApieContext $apieContext, public readonly ActionResponseStatus $status)
    {
    }

    public static function createCreationSuccess(ApieFacade $apieFacade, ApieContext $apieContext, mixed $result, mixed $resource): self
    {
        $res = new self($apieFacade, $apieContext, ActionResponseStatus::CREATED);
        $res->result = $result;
        $res->resource = $resource;
        return $res;
    }

    public static function createRunSuccess(ApieFacade $apieFacade, ApieContext $apieContext, mixed $result, mixed $resource): self
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
            ActionResponseStatus::CLIENT_ERROR => 400,
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
