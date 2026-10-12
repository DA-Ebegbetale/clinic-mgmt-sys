<?php
declare(strict_types=1);

namespace App\Application\Doctor\Mapper;

use App\Domain\Doctor\DoctorAuditLog;
use DateTimeImmutable;

class DoctorActionMapper
{
    public static function toEntity(array $row): DoctorAuditLog
    {
        $log = (new \ReflectionClass(DoctorAuditLog::class))->newInstanceWithoutConstructor();
        
        $set = function($property, $value) use ($log) {
            $p = new \ReflectionProperty(DoctorAuditLog::class, $property);
            $p->setValue($log, $value);
        };

        $set('id', $row['id'] ? (int)$row['id'] : null);
        $set('action', $row['action']);
        $set('targetType', $row['target_type']);
        $set('targetUuid', $row['target_uuid'] ?? null);
        $set('doctorId', (int)$row['doctor_id']);
        $set('meta', $row['meta'] ?? null);
        $set('createdAt', new DateTimeImmutable($row['created_at']));

        return $log;
    }

    public static function toDatabase(DoctorAuditLog $log): array
    {
        return [
            'action' => $log->getAction(),
            'target_type' => $log->getTargetType(),
            'target_uuid' => $log->getTargetUuid(),
            'doctor_id' => $log->getDoctorId(),
            'meta' => $log->getMeta(),
            'created_at' => $log->getCreatedAt()
        ];
    }
}