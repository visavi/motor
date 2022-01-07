<?php

declare(strict_types=1);

namespace App\Services;

use Slim\Views\Twig;
use Twig\Error\Error;

/**
 * Page navigation
 *
 * @license Code and contributions have MIT License
 * @link    https://visavi.net
 * @author  Alexander Grigorev <admin@visavi.net>
 * @version 1.0
 */
class Paginator
{
    public int $limit;
    public int $total;
    public int $crumbs;
    public int $offset;
    public int $page;

    public function __construct(
        protected Twig $view
    ) {}

    public function create(int $total, int $limit = 10, int $crumbs = 1): self
    {
        $this->limit  = $limit;
        $this->total  = $total;
        $this->crumbs = $crumbs;
        $this->page   = $this->page();
        $this->offset = $this->offset();

        return $this;
    }

    /**
     * Get offset
     *
     * @return int
     */
    public function offset(): int
    {
        if ($this->total && $this->page * $this->limit >= $this->total) {
            $this->page = (int) ceil($this->total / $this->limit);
        }

        return $this->page * $this->limit - $this->limit;
    }

    /**
     * Get current page
     *
     * @return int
     */
    public function page(): int
    {
        return ! empty($_GET['page']) ? abs((int) $_GET['page']) : 1;
    }

    /**
     * Get items
     *
     * @return array Сформированный блок с кнопками страниц
     */
    public function items(): array
    {
        if (! $this->total) {
            return [];
        }

        $pages = [];
        $pg_cnt = (int) ceil($this->total / $this->limit);
        $idx_fst = max($this->page - $this->crumbs, 1);
        $idx_lst = min($this->page + $this->crumbs, $pg_cnt);

        if ($this->page !== 1) {
            $pages[] = [
                'page' => $this->page - 1,
                'name' => '«',
            ];
        }

        if ($this->page > $this->crumbs + 1) {
            $pages[] = [
                'page' => 1,
                'name' => 1,
            ];
            if ($this->page !== $this->crumbs + 2) {
                $pages[] = [
                    'separator' => true,
                ];
            }
        }

        for ($i = $idx_fst; $i <= $idx_lst; $i++) {
            if ($i === $this->page) {
                $pages[] = [
                    'current' => true,
                    'name'    => $i,
                ];
            } else {
                $pages[] = [
                    'page' => $i,
                    'name' => $i,
                ];
            }
        }

        if ($this->page < $pg_cnt - $this->crumbs) {
            if ($this->page !== $pg_cnt - $this->crumbs - 1) {
                $pages[] = [
                    'separator' => true,
                ];
            }
            $pages[] = [
                'page' => $pg_cnt,
                'name' => $pg_cnt,
            ];
        }

        if ($this->page !== $pg_cnt) {
            $pages[] = [
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
     * @throws Error
     */
    public function links(): string
    {
        return $this->view->fetch(
            'app/_paginator.twig',
            ['pages' => $this->items()]
        );
    }
}
