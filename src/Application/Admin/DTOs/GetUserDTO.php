<?php
declare(strict_types=1);
namespace App\Application\Admin\DTOs;

class GetUserDTO {
    public function __construct(public readonly string $uuid) {}
}