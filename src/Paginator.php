<?php
/**
 * Page navigation
 *
 * @license Code and contributions have MIT License
 * @link    http://visavi.net
 * @author  Alexander Grigorev <admin@visavi.net>
 * @version 1.0
 */

namespace App;

class Paginator
{
    public int $limit;
    public int $total;
    public int $crumbs;
    public int $offset;
    public int $page;

    public function __construct($limit, $total, $crumbs = 1)
    {
        $this->limit  = $limit;
        $this->total  = $total;
        $this->crumbs = $crumbs;
        $this->page   = $this->page();
        $this->offset = $this->offset();
    }

    /**
     * Get offset
     *
     * @return int
     */
    public function offset(): int
    {
        if ($this->page * $this->limit >= $this->total) {
            $this->page = (int) ceil($this->total / $this->limit);
        }

        return $this->page * $this->limit - $this->limit;
    }

    /**
     * Get offset
     *
     * @return int
     */
/*    public function offset(): int
    {
        $totalPage = ceil($this->total / $this->limit);
        $curOffset = ($this->page - 1) * $this->limit;
        $lastOffset = ($totalPage - 1) * $this->limit;

        return $this->page > $totalPage ? $lastOffset : $curOffset;
    }*/

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
                    'name' => ' ... ',
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
                    'name'      => ' ... ',
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
     */
    public function links(): string
    {
        return (new View())->render('bootstrap', ['pages' => $this->items()]);
    }
}
