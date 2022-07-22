<?php
namespace Apie\Core\ContextBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\Exceptions\WrongContentTypeException;
use Psr\Http\Message\RequestInterface;

class CheckWrongContentTypeError implements ContextBuilderInterface
{
    public function process(ApieContext $context): ApieContext
    {
        if (
            $context->hasContext(RequestInterface::class)
            && !$context->hasContext(ContextBuilderInterface::RAW_CONTENTS)
            && $context->getContext(RequestInterface::class)->getMethod() !== 'GET'
        ) {
            throw new WrongContentTypeException(
                $context->getContext(RequestInterface::class)->getHeader('Content-Type')[0] ?? '(null)'
            );
        }
        return $context;
    }
}
