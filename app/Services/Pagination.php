<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Pagination
{
    protected int $limit;
    protected int $total;
    protected int $crumbs;
    protected int $offset;
    protected int $page;
    protected Paginator $items;
    protected ?string $path = null;
    protected array $appends = [];

    public function __construct(
        //protected ?string $viewPath = null,
        //protected ?string $pageName = null,
    ) {
        //$this->viewPath = $viewPath ?: __DIR__ . '/views/bootstrap5.php';
        //$this->pageName = $pageName ?: 'page';
    }

    /**
     * Create paginate
     */
    public function paginate(QueryBuilder $query, int $limit = 10, int $crumbs = 1): self
    {
        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult($limit * ($this->currentPage() - 1))
            ->setMaxResults($limit);

        $this->total = $paginator->count();
        $this->limit  = $limit;
        $this->crumbs = $crumbs;
        $this->page   = $this->currentPage();
        $this->offset = $this->offset();
        $this->items = $paginator;

        return $this;
    }

    /**
     * Get offset
     *
     * @return int
     */
    public function offset(): int
    {
        if ($this->total === 0) {
            $this->page = 1;
        } elseif ($this->total && $this->page * $this->limit >= $this->total) {
            $this->page = (int) ceil($this->total / $this->limit);
        }

        return $this->page * $this->limit - $this->limit;
    }

    /**
     * Get items
     */
    public function items(): Paginator
    {
        return $this->items;
    }

    /**
     * Get current page
     *
     * @return int
     */
    public function currentPage(): int
    {
        return ! empty($_GET['page']) ? abs((int) $_GET['page']) : 1;
    }

    /**
     * Get total items
     *
     * @return int
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Get pages
     *
     * @return array
     */
    public function pages(): array
    {
        if (! $this->total) {
            return [];
        }

        $pages      = [];
        $pageCount  = (int) ceil($this->total / $this->limit);
        $indexFirst = max($this->page - $this->crumbs, 1);
        $indexLast  = min($this->page + $this->crumbs, $pageCount);

        if ($this->page !== 1) {
            $pages[] = [
                'link' => $this->buildUrl($this->page - 1),
                'page' => $this->page - 1,
                'name' => '«',
            ];
        }

        if ($this->page > $this->crumbs + 1) {
            $pages[] = [
                'link' => $this->buildUrl(1),
                'page' => 1,
                'name' => 1,
            ];
            if ($this->page !== $this->crumbs + 2) {
                $pages[] = [
                    'separator' => true,
                ];
            }
        }

        for ($i = $indexFirst; $i <= $indexLast; $i++) {
            if ($i === $this->page) {
                $pages[] = [
                    'current' => true,
                    'name'    => $i,
                ];
            } else {
                $pages[] = [
                    'link' => $this->buildUrl($i),
                    'page' => $i,
                    'name' => $i,
                ];
            }
        }

        if ($this->page < $pageCount - $this->crumbs) {
            if ($this->page !== $pageCount - $this->crumbs - 1) {
                $pages[] = [
                    'separator' => true,
                ];
            }

            $pages[] = [
                'link' => $this->buildUrl($pageCount),
                'page' => $pageCount,
                'name' => $pageCount,
            ];
        }

        if ($this->page !== $pageCount) {
            $pages[] = [
                'link' => $this->buildUrl($this->page + 1),
                'page' => $this->page + 1,
                'name' => '»',
            ];
        }

        return $pages;
    }

    /**
     * Get rendered links
     *
     * @return string
     */
    public function links(): string
    {
        ob_start();
        $pages = $this->pages();
        include(__DIR__ . '/../../resources/views/app/_paginator.php');

        return ob_get_clean();
    }

    /**
     * Add path
     *
     * @param string $path
     *
     * @return void
     */
    public function withPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Append params url
     *
     * @param array $appends
     *
     * @return void
     */
    public function appends(array $appends): void
    {
        $this->appends = $appends;
    }

    /**
     * Build url
     *
     * @param int $page
     *
     * @return string
     */
    protected function buildUrl(int $page): string
    {
        return $this->path . http_build_query(['page' => $page] + $this->appends);
    }
}
