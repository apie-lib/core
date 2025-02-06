<?php
namespace Apie\Core\Datalayers\Search;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\StringHashmap;
use Apie\Core\ValueObjects\Utils;
use Apie\DoctrineEntityDatalayer\Enums\SortingOrder;

final class QuerySearch
{
    private ?string $textSearch;

    private StringHashmap $searches;

    private StringHashmap $orderBy;

    private ApieContext $apieContext;

    public function __construct(
        private int $pageIndex,
        private int $itemsPerPage = 20,
        ?string $textSearch = null,
        ?StringHashmap $searches = null,
        ?StringHashmap $orderBy = null,
        ?ApieContext $apieContext = null,
    ) {
        $this->textSearch = $textSearch;
        $this->searches = $searches ?? new StringHashmap();
        $this->orderBy = $orderBy ?? new StringHashmap();
        $this->apieContext = $apieContext ?? new ApieContext();
    }

    public function getApieContext(): ApieContext
    {
        return $this->apieContext;
    }

    /**
     * @param array<string, string|int|array<string, mixed>> $input
     */
    public static function fromArray(array $input, ?ApieContext $apieContext = new ApieContext()): self
    {
        $pageIndex = $input['page'] ?? 0;
        $itemsPerPage = max($input['items_per_page'] ?? 20, 1);
        $data = is_array($input['query'] ?? '') ? $input['query'] : [];
        $orderBy = $input['order_by'] ?? [];
        if (!is_array($orderBy) && !empty($orderBy)) {
            $orderBy = explode(',', Utils::toString($orderBy));
        }
        $constructedOrderBy = [];
        foreach ($orderBy as $column) {
            if (str_starts_with($column, '+')) {
                $constructedOrderBy[substr($column, 1)] = SortingOrder::ASCENDING->value;
            } elseif (str_starts_with($column, '-')) {
                $constructedOrderBy[substr($column, 1)] = SortingOrder::DESCENDING->value;
            } else {
                $constructedOrderBy[$column] = SortingOrder::ASCENDING->value;
            }
        }
        return new QuerySearch(
            $pageIndex,
            $itemsPerPage,
            $input['search'] ?? null,
            new StringHashmap($data),
            new StringHashmap($constructedOrderBy),
            $apieContext
        );
    }

    public function toHttpQuery(): string
    {
        $query = [];
        if ($this->pageIndex > 0) {
            $query['page'] = $this->pageIndex;
        }
        if ($this->itemsPerPage !== 20) {
            $query['items_per_page'] = $this->itemsPerPage;
        }
        if ($this->textSearch !== null) {
            $query['search'] = $this->textSearch;
        }
        if (0 !== $this->searches->count()) {
            $query['query'] = $this->searches->toArray();
        }
        return empty($query) ? '' : '?' . http_build_query($query);
    }

    public function withPageIndex(int $pageIndex): self
    {
        return new self($pageIndex, $this->itemsPerPage, $this->textSearch, $this->searches);
    }

    public function getPageIndex(): int
    {
        return $this->pageIndex;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getTextSearch(): ?string
    {
        return $this->textSearch;
    }

    public function getSearches(): StringHashmap
    {
        return $this->searches;
    }

    public function getOrderBy(): StringHashmap
    {
        return $this->orderBy;
    }
}
