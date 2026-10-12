<?php
declare(strict_types=1);

namespace App\Application\Shared\Validators;

use InvalidArgumentException;
use DateTimeImmutable;

class Validator
{
    public static function validateRequiredString(string $value, string $fieldName): string
    {
        $sanitized = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        if (empty($sanitized)) {
            throw new InvalidArgumentException("{$fieldName} is required.");
        }
        return $sanitized;
    }

    public static function validateEmail(string $email): string
    {
        $sanitized = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        if (!filter_var($sanitized, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Please provide a valid email address.");
        }
        return $sanitized;
    }

    public static function validatePastOrPresentDate(string $date, string $fieldName): string
    {
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $date) ?: DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date);
        
        if (!$dateTime) {
            throw new InvalidArgumentException("Invalid date format for {$fieldName}.");
        }
        
        if ($dateTime > new DateTimeImmutable()) {
            throw new InvalidArgumentException("{$fieldName} cannot be a future date and time.");
        }
        
        return $dateTime->format('Y-m-d H:i:s');
    }

    public static function validateFutureDate(string $date, string $fieldName): string
    {
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d', $date);
        
        if (!$dateTime) {
            throw new InvalidArgumentException("Invalid date format for {$fieldName}. Use YYYY-MM-DD.");
        }
        
        // Strip time from current date for fair comparison
        $today = (new DateTimeImmutable())->setTime(0, 0, 0);
        
        if ($dateTime < $today) {
            throw new InvalidArgumentException("{$fieldName} must be today or a future date.");
        }
        
        return $dateTime->format('Y-m-d');
    }

    public static function validateAgeLimit(string $dateOfBirth, int $minimumAge = 18): string
    {
        $dob = DateTimeImmutable::createFromFormat('Y-m-d', $dateOfBirth);
        
        if (!$dob) {
            throw new InvalidArgumentException("Invalid Date of Birth format.");
        }

        $now = new DateTimeImmutable();
        $age = $now->diff($dob)->y;

        if ($age < $minimumAge) {
            throw new InvalidArgumentException("User must be at least {$minimumAge} years old to be registered as staff.");
        }
        
        // Sanity check for age
        if ($age > 130) {
            throw new InvalidArgumentException("Please enter a valid Date of Birth.");
        }

        return $dob->format('Y-m-d');
    }
}