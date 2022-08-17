<?php
namespace Apie\Core\ContextBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\RequestMethod;
use Psr\Http\Message\RequestInterface;

/**
 * Creates an ApieContext from a list of builders. The context object is used everywhere as a mediator.
 */
final class ContextBuilderFactory
{
    /**
     * @var ContextBuilderInterface[]
     */
    private array $builders;

    public function __construct(ContextBuilderInterface... $builders)
    {
        $this->builders = $builders;
    }

    public static function create(): self
    {
        return new self(
        );
    }

    /**
     * @param array<string|int, mixed> $additionalData
     */
    private function createBaseContext(array $additionalData): ApieContext
    {
        return new ApieContext([
            ...$additionalData,
            ContextBuilderFactory::class => $this,
        ]);
    }

    /**
     * @param array<string|int, mixed> $additionalData
     */
    public function createGeneralContext(array $additionalData): ApieContext
    {
        $context = $this->createBaseContext($additionalData);
        foreach ($this->builders as $builder) {
            $context = $builder->process($context);
        }
        return $context;
    }

    /**
     * @param array<string|int, mixed> $additionalData
     */
    public function createFromRequest(RequestInterface $request, array $additionalData = []): ApieContext
    {
        $context = $this->createBaseContext($additionalData)
            ->registerInstance($request)
            ->withContext(RequestMethod::class, RequestMethod::from($request->getMethod()));
        foreach ($this->builders as $builder) {
            $context = $builder->process($context);
        }
        return $context;
    }
}
