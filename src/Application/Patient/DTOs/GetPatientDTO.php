<?php
declare(strict_types=1);
namespace App\Application\Patient\DTOs;

class GetPatientDTO {
    public function __construct(public readonly string $uuid) {}
}