<?php
declare(strict_types=1);

namespace App\Domain\Doctor;

use DateTimeImmutable;

class DoctorAuditLog
{
    private ?int $id;
    private string $action;
    private string $targetType;
    private ?string $targetUuid;
    private int $doctorId;
    private ?string $meta;
    private DateTimeImmutable $createdAt; 

    private function __construct() {}

    public static function logAction(
        string $action,
        string $targetType,
        ?string $targetUuid,
        int $doctorId,
        ?array $meta = null
    ): self {
        $log = new self();
        $log->id = null;
        $log->action = $action;
        $log->targetType = $targetType;
        $log->targetUuid = $targetUuid;
        $log->doctorId = $doctorId;
        $log->meta = $meta ? json_encode($meta) : null;
        $log->createdAt = new DateTimeImmutable();
        return $log;
    }

    public function getId(): ?int { return $this->id; }
    public function getAction(): string { return $this->action; }
    public function getTargetType(): string { return $this->targetType; }
    public function getTargetUuid(): ?string { return $this->targetUuid; }
    public function getDoctorId(): int { return $this->doctorId; }
    public function getMeta(): ?string { return $this->meta; }
    public function getCreatedAt(): string { return $this->createdAt->format('Y-m-d H:i:s'); }
}