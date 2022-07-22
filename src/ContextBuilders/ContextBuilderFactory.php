<?php
namespace Apie\Core\ContextBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\RequestMethod;
use Psr\Http\Message\RequestInterface;

final class ContextBuilderFactory
{
    private array $builders;

    public function __construct(ContextBuilderInterface... $builders)
    {
        $this->builders = $builders;
    }

    public static function create(): self
    {
        return new self(
            new ExtractRawJsonContentsFromBody(),
            new CheckWrongContentTypeError(),
        );
    }

    private function createBaseContext(): ApieContext
    {
        return new ApieContext([
            ContextBuilderFactory::class => $this,
        ]);
    }

    public function createFromRequest(RequestInterface $request): ApieContext
    {
        $context = $this->createBaseContext()
            ->registerInstance($request)
            ->withContext(RequestMethod::class, RequestMethod::from($request->getMethod()));
        foreach ($this->builders as $builder) {
            $context = $builder->process($context);
        }
        return $context;
    }
}
