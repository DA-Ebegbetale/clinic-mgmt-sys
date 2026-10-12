<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Admin\AdminAuditLog;
use App\Domain\Admin\AdminAuditRepositoryInterface;
use App\Infrastructure\Database\PDOConnection;
use App\Application\Admin\Mapper\AdminActionMapper;
use PDO;

class MySQLAdminAuditRepository implements AdminAuditRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function save(AdminAuditLog $log): void
    {
        $data = AdminActionMapper::toDatabase($log);
        $stmt = $this->db->prepare("
    INSERT INTO admin_action_logs
            (action, target_type, target_id, target_uuid, admin_id, reason, meta, created_at)
        VALUES
            (:action, :target_type, :target_id, :target_uuid, :admin_id, :reason, :meta, :created_at)
    ");
        $stmt->execute([
            'action' => $data['action'],
            'target_type' => $data['target_type'],
            'target_id' => $data['target_id'],
            'target_uuid' => $data['target_uuid'],
            'admin_id' => $data['admin_id'],
            'reason' => $data['reason'],
            'meta' => $data['meta'],
            'created_at' => $data['created_at']
        ]);
    }
    public function getPaginatedLogs(int $limit, int $offset, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = 'SELECT * FROM admin_action_logs';
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
            'SELECT COUNT(*) as total FROM admin_action_logs' .
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