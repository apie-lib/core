<?php
namespace Apie\Core\ContextBuilders;

use Apie\Core\Context\ApieContext;
use Psr\Http\Message\RequestInterface;

class ExtractRawJsonContentsFromBody implements ContextBuilderInterface
{
    public function process(ApieContext $context): ApieContext
    {
        if (!$context->hasContext(RequestInterface::class)) {
            return $context;
        }
        /* @var RequestInterface */
        $request = $context->getContext(RequestInterface::class);
        $body = $request->getBody();
        if (!$body) {
            return $context;
        }
        $stringBody = (string) $body;
        $contentType = $request->getHeader('content-type')[0] ?? null;
        if ($contentType === 'application/json') {
            return $context->withContext(ContextBuilderInterface::RAW_CONTENTS, json_decode($stringBody, true));
        }
        return $context;
    }
}
