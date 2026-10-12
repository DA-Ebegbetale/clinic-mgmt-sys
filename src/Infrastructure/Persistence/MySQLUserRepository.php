<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Database\PDOConnection;
use App\Application\Auth\Mapper\UserMapper;
use PDO;

class MySQLUserRepository implements UserRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function save(User $user): void
    {
        $data = UserMapper::toDatabase($user);
        $stmt = $this->db->prepare("
            INSERT INTO users (uuid, first_name, last_name, middle_name, email, phone, password_hash, role, is_active, created_at, updated_at)
            VALUES (:uuid, :first_name, :last_name, :middle_name, :email, :phone, :password_hash, :role, :is_active, :created_at, :updated_at)
        ");
        $stmt->execute([
            'uuid' => $data['uuid'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => $data['password_hash'],
            'role' => $data['role'],
            'is_active' => $data['is_active'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at']
        ]);
    }

    public function update(User $user): void
    {
        $data = UserMapper::toDatabase($user);
        $stmt = $this->db->prepare("
            UPDATE users 
            SET first_name = :first_name, last_name = :last_name, middle_name = :middle_name,
                email = :email, phone = :phone, password_hash = :password_hash, 
                is_active = :is_active, last_login_at = :last_login_at, updated_at = :updated_at 
            WHERE uuid = :uuid
        ");
        $stmt->execute([
            'uuid' => $data['uuid'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => $data['password_hash'],
            'is_active' => $data['is_active'],
            'last_login_at' => $data['last_login_at'],
            'updated_at' => $data['updated_at']
        ]);
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? UserMapper::toEntity($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ? UserMapper::toEntity($row) : null;
    }

    public function findByUuid(string $uuid): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE uuid = :uuid LIMIT 1");
        $stmt->execute(['uuid' => $uuid]);
        $row = $stmt->fetch();

        return $row ? UserMapper::toEntity($row) : null;
    }
    public function search(\App\Application\Admin\DTOS\SearchUserDTO $dto): array
    {
        $whereClause = "1=1"; // Default fallback (returns all if no search value)
        $params = [];

        // Database-level filtering for inactive users
        if ($dto->isActive !== null) {
            $whereClause .= " AND is_active = :is_active";
            $params['is_active'] = $dto->isActive;
        }

        if (!empty($dto->searchValue)) {
            // Mapping the UI dropdown to safe SQL columns
            switch ($dto->searchField) {
                case 'uuid':
                    $whereClause .= " AND uuid = :val";
                    $params['val'] = $dto->searchValue;
                    break;

                case 'first_name':
                    $whereClause .= " AND first_name LIKE :val";
                    $params['val'] = '%' . $dto->searchValue . '%';
                    break;

                case 'last_name':
                    $whereClause .= " AND last_name LIKE :val";
                    $params['val'] = '%' . $dto->searchValue . '%';
                    break;

                case 'email':
                    $whereClause .= " AND email = :val";
                    $params['val'] = $dto->searchValue;
                    break;

                case 'phone':
                    $whereClause .= " AND phone = :val";
                    $params['val'] = $dto->searchValue;
                    break;

                case 'full_name':
                    $whereClause .= " AND CONCAT_WS(' ', first_name, middle_name, last_name) LIKE :val";
                    $params['val'] = '%' . $dto->searchValue . '%';
                    break;

                default:
                    break;
            }
        }

        // Get Total Count necessary for for Frontend Pagination Math)
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE " . $whereClause);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Get Paginated Results
        // Since LIMIT and OFFSET cannot be bound as standard PDO string params safely without casting, 
        // we therefore inject the validated integers directly.
        $limit = $dto->limit;
        $offset = $dto->getOffset();

        $sql = "SELECT * FROM users WHERE {$whereClause} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $users = [];
        while ($row = $stmt->fetch()) {
            $users[] = \App\Application\Auth\Mapper\UserMapper::toEntity($row);
        }

        return [
            'users' => $users,
            'total' => $total
        ];
    }
}