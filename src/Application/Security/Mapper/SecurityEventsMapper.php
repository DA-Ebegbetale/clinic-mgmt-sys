<?php
declare(strict_types=1);

namespace App\Application\Security\Mapper;

use App\Domain\Security\SecurityAuditLog;
use DateTimeImmutable;

class SecurityEventsMapper
{
    public static function toEntity(array $row): SecurityAuditLog
    {
        $log = (new \ReflectionClass(SecurityAuditLog::class))->newInstanceWithoutConstructor();
        
        $set = function($property, $value) use ($log) {
            $p = new \ReflectionProperty(SecurityAuditLog::class, $property);
            $p->setValue($log, $value);
        };

        $set('id', $row['id'] ? (int)$row['id'] : null);
        $set('eventType', $row['event_type']);
        $set('userId', $row['user_id'] ?? null);
        $set('userUuid', $row['user_uuid'] ?? null);
        $set('ipAddress', $row['ip_address'] ?? null);
        $set('userAgent', $row['user_agent'] ?? null);
        $set('deviceInfo', $row['device_info'] ?? null);
        $set('details', $row['details'] ?? null);
        $set('createdAt', new DateTimeImmutable($row['created_at']));

        return $log;
    }

    public static function toDatabase(SecurityAuditLog $log): array
    {
        return [
            'event_type' => $log->getEventType(),
            'user_id' => $log->getUserId(),
            'user_uuid' => $log->getUserUuid(),
            'ip_address' => $log->getIpAddress(),
            'user_agent' => $log->getUserAgent(),
            'device_info' => $log->getDeviceInfo(),
            'details' => $log->getDetails(),
            'created_at' => $log->getCreatedAt()
        ];
    }
}