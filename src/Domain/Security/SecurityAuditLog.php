<?php
declare(strict_types=1);

namespace App\Domain\Security;

use DateTimeImmutable;

class SecurityAuditLog
{
    private ?int $id;
    private string $eventType;
    private ?int $userId;
    private ?string $userUuid;
    private ?string $ipAddress;
    private ?string $userAgent;
    private ?string $deviceInfo;
    private ?string $details;
    private DateTimeImmutable $createdAt;

    private function __construct()
    {
    }

    public static function logEvent(
        string $eventType,
        int $userId,
        ?string $userUuid = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $deviceInfo = null,
        ?string $details = null
    ): self {
        $log = new self();
        $log->id = null;
        $log->eventType = $eventType;
        $log->userId = $userId;
        $log->userUuid = $userUuid;
        $log->ipAddress = $ipAddress;
        $log->userAgent = $userAgent;
        $log->deviceInfo = $deviceInfo;
        $log->details = $details;
        $log->createdAt = new DateTimeImmutable();
        return $log;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEventType(): string
    {
        return $this->eventType;
    }
    public function getUserId(): ?int
    {
        return $this->userId;
    }
    public function getUserUuid(): ?string
    {
        return $this->userUuid;
    }
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }
    public function getDeviceInfo(): ?string
    {
        return $this->deviceInfo;
    }
    public function getDetails(): ?string
    {
        return $this->details;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }
}