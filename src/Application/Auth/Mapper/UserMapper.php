<?php
declare(strict_types=1);

namespace App\Application\Auth\Mapper;

use App\Domain\User\User;
use DateTimeImmutable;

class UserMapper
{
    public static function toEntity(array $row): User
    {
        $user = (new \ReflectionClass(User::class))->newInstanceWithoutConstructor();
        
        $set = function($property, $value) use ($user) {
            $p = new \ReflectionProperty(User::class, $property);
            $p->setValue($user, $value);
        };

        $set('id', $row['id'] ? (int)$row['id'] : null);
        $set('uuid', $row['uuid']);
        $set('firstName', $row['first_name']);
        $set('lastName', $row['last_name']);
        $set('middleName', $row['middle_name'] ?? null);
        $set('email', $row['email']);
        $set('phone', $row['phone'] ?? null);
        $set('passwordHash', $row['password_hash']);
        $set('role', $row['role']);
        $set('isActive', (int)$row['is_active']);
        $set('lastLoginAt', $row['last_login_at'] ? new DateTimeImmutable($row['last_login_at']) : null);
        $set('createdAt', $row['created_at'] ? new DateTimeImmutable($row['created_at']) : null);
        $set('updatedAt', $row['updated_at'] ? new DateTimeImmutable($row['updated_at']) : null);

        return $user;
    }

    public static function toDatabase(User $user): array
    {
        return [
            'uuid' => $user->getUuid(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'middle_name' => $user->getMiddleName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'password_hash' => $user->getPasswordHash(),
            'role' => $user->getRole(),
            'is_active' => $user->getIsActive(),
            'last_login_at' => $user->getLastLoginAt(),
            'created_at' => $user->getCreatedAt(),
            'updated_at' => $user->getUpdatedAt()
        ];
    }
}