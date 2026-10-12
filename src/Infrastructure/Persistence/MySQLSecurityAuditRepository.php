<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Security\SecurityAuditLog;
use App\Domain\Security\SecurityAuditRepositoryInterface;
use App\Infrastructure\Database\PDOConnection;
use App\Application\Security\Mapper\SecurityEventsMapper;
use PDO;

class MySQLSecurityAuditRepository implements SecurityAuditRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function save(SecurityAuditLog $log): void
    {
        $data = SecurityEventsMapper::toDatabase($log);
        $stmt = $this->db->prepare("
            INSERT INTO security_event_logs (event_type, user_id, user_uuid, ip_address, user_agent, device_info, details, created_at)
            VALUES (:event_type, :user_id, :user_uuid, :ip_address, :user_agent, :device_info, :details, :created_at)
        ");
        $stmt->execute([
            'event_type' => $data['event_type'],
            'user_id' => $data['user_id'],
            'user_uuid' => $data['user_uuid'],
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'],
            'device_info' => $data['device_info'],
            'details' => $data['details'],
            'created_at' => $data['created_at']
        ]);
    }
    public function getPaginatedLogs(int $limit, int $offset, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = 'SELECT * FROM security_event_logs';
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
            'SELECT COUNT(*) as total FROM security_event_logs' .
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
