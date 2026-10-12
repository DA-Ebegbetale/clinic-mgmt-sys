<?php
declare(strict_types=1);

namespace App\Application\Admin\Mapper;

use App\Domain\Admin\AdminAuditLog;
use DateTimeImmutable;

class AdminActionMapper
{
    public static function toEntity(array $row): AdminAuditLog
    {
        $log = (new \ReflectionClass(AdminAuditLog::class))->newInstanceWithoutConstructor();
        
        $set = function($property, $value) use ($log) {
            $p = new \ReflectionProperty(AdminAuditLog::class, $property);
            $p->setValue($log, $value);
        };

        $set('id', $row['id'] ? (int)$row['id'] : null);
        $set('action', $row['action']);
        $set('targetType', $row['target_type']);
        $set('targetId', $row['target_id'] ? (int)$row['target_id'] : null);
        $set('targetUuid', $row['target_uuid'] ?? null);
        $set('adminId', (int)$row['admin_id']);
        $set('reason', $row['reason'] ?? null);
        $set('meta', $row['meta'] ?? null);
        $set('createdAt', new DateTimeImmutable($row['created_at']));

        return $log;
    }

    public static function toDatabase(AdminAuditLog $log): array
    {
        return [
            'action' => $log->getAction(),
            'target_type' => $log->getTargetType(),
            'target_id' => $log->getTargetId(),
            'target_uuid' => $log->getTargetUuid(),
            'admin_id' => $log->getAdminId(),
            'reason' => $log->getReason(),
            'meta' => $log->getMeta(),
            'created_at' => $log->getCreatedAt()
        ];
    }
}