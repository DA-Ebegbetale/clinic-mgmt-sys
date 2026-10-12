<?php
declare(strict_types=1);

namespace App\Application\Admin\DTOs;

class SearchUserDTO
{
    public readonly string $searchField;
    public readonly string $searchValue;
    public readonly int $page;
    public readonly int $limit;
    public readonly ?int $isActive;

    public function __construct(string $searchField, string $searchValue, int $page = 1, int $limit = 10, ?int $isActive = null)
    {
        $this->searchField = $searchField;
        $this->searchValue = $searchValue;
        $this->page = $page;
        $this->limit = $limit;
        $this->isActive = $isActive;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}
