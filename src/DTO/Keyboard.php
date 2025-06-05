<?php

namespace App\DTO;

class Keyboard
{
    public function __construct(
        public array $items,
        public string $prefix,
        public string $textField,
        public string $keyField,
        public ?string $paginationPrefix = null,
        public ?int $nextPageOffset = null,
    ) {
    }
}
