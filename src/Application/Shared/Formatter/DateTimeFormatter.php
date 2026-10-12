<?php
declare(strict_types=1);

namespace App\Application\Shared\Formatter;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

class DateTimeFormatter
{
    private const DISPLAY_FORMAT = 'M d, Y, h:i A'; // e.g., Jul 17, 2026, 10:32 AM
    private const FORM_DATE_FORMAT = 'Y-m-d';       // For <input type="date">
    private const FORM_DATETIME_FORMAT = 'Y-m-d\TH:i'; // For <input type="datetime-local">

    public static function toHuman(string|DateTimeImmutable|null $date, string $timezone = 'UTC'): string
    {
        if ($date === null) {
            return 'N/A';
        }

        try {
            $dateTime = $date instanceof DateTimeImmutable 
                ? $date 
                : new DateTimeImmutable($date, new DateTimeZone('UTC'));
            
            return $dateTime->setTimezone(new DateTimeZone($timezone))->format(self::DISPLAY_FORMAT);
        } catch (Exception) {
            return 'Invalid Date';
        }
    }

    public static function toFormDate(string|DateTimeImmutable|null $date): string
    {
        if ($date === null) {
            return '';
        }
        try {
            $dateTime = $date instanceof DateTimeImmutable ? $date : new DateTimeImmutable($date);
            return $dateTime->format(self::FORM_DATE_FORMAT);
        } catch (Exception) {
            return '';
        }
    }

    public static function toFormDateTime(string|DateTimeImmutable|null $date): string
    {
        if ($date === null) {
            return '';
        }
        try {
            $dateTime = $date instanceof DateTimeImmutable ? $date : new DateTimeImmutable($date);
            return $dateTime->format(self::FORM_DATETIME_FORMAT);
        } catch (Exception) {
            return '';
        }
    }
}