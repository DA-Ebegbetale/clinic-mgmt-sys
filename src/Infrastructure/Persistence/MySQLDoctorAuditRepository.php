<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Doctor\DoctorAuditLog;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;
use App\Infrastructure\Database\PDOConnection;
use App\Application\Doctor\Mapper\DoctorActionMapper;
use PDO;

class MySQLDoctorAuditRepository implements DoctorAuditRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function save(DoctorAuditLog $log): void
    {
        $data = DoctorActionMapper::toDatabase($log);

        $stmt = $this->db->prepare("
        INSERT INTO doctor_action_logs
            (action, target_type, target_uuid, doctor_id, meta, created_at)
        VALUES
            (:action, :target_type, :target_uuid, :doctor_id, :meta, :created_at)
    ");

        $stmt->execute([
            'action' => $data['action'],
            'target_type' => $data['target_type'],
            'target_uuid' => $data['target_uuid'],
            'doctor_id' => $data['doctor_id'],
            'meta' => $data['meta'],
            'created_at' => $data['created_at']
        ]);
    }
    public function getPaginatedLogs(int $limit, int $offset, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = 'SELECT * FROM doctor_action_logs';
        $params = [];

        if ($dateFrom || $dateTo) {
            $query .= ' WHERE';
            if ($dateFrom) {
                $query .= ' created_at >= ?';
                $params[] = $dateFrom . ' 00:00:00';
            }
            if ($dateTo) {
                $query .= ($dateFrom ? ' AND' : '') . ' created_at <= ?';
                $params[] = $dateTo . ' 23:59:59';
            }
        }

        $query .= ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->db->prepare(
            'SELECT COUNT(*) as total FROM doctor_action_logs' .
            ($dateFrom || $dateTo ? ' WHERE' . ($dateFrom ? ' created_at >= ?' : '') . ($dateTo ? ($dateFrom ? ' AND' : '') . ' created_at <= ?' : '') : '')
        );
        $countParams = [];

        if ($dateFrom) {
            $countParams[] = $dateFrom . ' 00:00:00';
        }

        if ($dateTo) {
            $countParams[] = $dateTo . ' 23:59:59';
        }

        $countStmt->execute($countParams);
        $total = (int) $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        return ['logs' => $logs, 'total' => $total];
    }
}