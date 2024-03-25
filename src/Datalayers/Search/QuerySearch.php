<?php
namespace Apie\Core\Datalayers\Search;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\StringHashmap;

final class QuerySearch
{
    private ?string $textSearch;

    private StringHashmap $searches;

    public function __construct(
        private int $pageIndex,
        private int $itemsPerPage = 20,
        ?string $textSearch = null,
        ?StringHashmap $searches = null,
        private ?ApieContext $apieContext = new ApieContext(),
    ) {
        $this->textSearch = $textSearch;
        $this->searches = null === $searches ? new StringHashmap() : $searches;
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
        return new QuerySearch(
            $pageIndex,
            $itemsPerPage,
            $input['search'] ?? null,
            new StringHashmap($data),
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
}
