<?php
declare(strict_types=1);

namespace App\Domain\Admin;

use DateTimeImmutable;

class AdminAuditLog
{
    private ?int $id;
    private string $action;
    private string $targetType;
    private ?int $targetId;
    private ?string $targetUuid;
    private int $adminId;
    private ?string $reason;
    private ?string $meta;
    private DateTimeImmutable $createdAt;

    private function __construct() {}

    public static function logAction(
        string $action,
        string $targetType,
        ?int $targetId,
        ?string $targetUuid,
        int $adminId,
        ?string $reason = null,
        ?array $meta = null
    ): self {
        $log = new self();
        $log->id = null;
        $log->action = $action;
        $log->targetType = $targetType;
        $log->targetId = $targetId;
        $log->targetUuid = $targetUuid;
        $log->adminId = $adminId;
        $log->reason = $reason;
        $log->meta = $meta ? json_encode($meta) : null;
        $log->createdAt = new DateTimeImmutable();
        return $log;
    }

    public function getId(): ?int { return $this->id; }
    public function getAction(): string { return $this->action; }
    public function getTargetType(): string { return $this->targetType; }
    public function getTargetId(): ?int { return $this->targetId; }
    public function getTargetUuid(): ?string { return $this->targetUuid; }
    public function getAdminId(): int { return $this->adminId; }
    public function getReason(): ?string { return $this->reason; }
    public function getMeta(): ?string { return $this->meta; }
    public function getCreatedAt(): string { return $this->createdAt->format('Y-m-d H:i:s'); }
}