<?php
declare(strict_types=1);

namespace App\Infrastructure\Container;

use RuntimeException;

class AppContainer
{
    private array $bindings = [];
    private array $instances = [];

    // Register how to build a class
    public function bind(string $key, callable $resolver): void
    {
        $this->bindings[$key] = $resolver;
    }

    // Retrieve an instantiated class
    public function get(string $key): mixed
    {
        // If we already built it, return the same instance (Singleton behavior for services/DB)
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (!isset($this->bindings[$key])) {
            throw new RuntimeException("No container binding found for: {$key}");
        }

        // Build it, save it, return it
        $this->instances[$key] = $this->bindings[$key]($this);
        return $this->instances[$key];
    }
}