<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Paginasi sederhana (wajib untuk tabel > 10 baris).
 *
 * $p = Paginator::fromRequest($totalBaris);
 * ... LIMIT :limit OFFSET :offset  (bind $p->perPage dan $p->offset())
 */
final class Paginator
{
    public function __construct(
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage
    ) {
    }

    public static function fromRequest(int $total): self
    {
        $perPage = max(1, (int) Config::get('app.per_page', 10));
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, (int) ($_GET['page'] ?? 1)), $lastPage);

        return new self($total, $page, $perPage);
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    public function lastPage(): int
    {
        return max(1, (int) ceil($this->total / $this->perPage));
    }
}
