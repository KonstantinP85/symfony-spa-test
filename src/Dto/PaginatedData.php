<?php

namespace App\Dto;

class PaginatedData
{
    public int $total;

    public int $limit;

    public int $page;

    public array $items;

    public function __construct(int $total, int $limit, int $page, array $items)
    {
        $this->total = $total;
        $this->limit = $limit;
        $this->page = $page;
        $this->items = $items;
    }
}