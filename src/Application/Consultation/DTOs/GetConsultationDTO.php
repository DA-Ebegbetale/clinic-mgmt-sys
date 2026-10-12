<?php
declare(strict_types=1);
namespace App\Application\Consultation\DTOs;

class GetConsultationDTO {
    public function __construct(public readonly string $uuid) {}
}