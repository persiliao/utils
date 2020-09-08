<?php

declare(strict_types=1);
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 *
 * @see https://www.github.com/persiliao
 */

namespace PersiLiao\Utils\Traits;

trait Container
{
    /**
     * @var array
     */
    protected static $container = [];

    /**
     * Add a value to container by identifier.
     *
     * @param mixed $value
     */
    public static function set(string $id, $value)
    {
        static::$container[$id] = $value;
    }

    /**
     * Finds an entry of the container by its identifier and returns it,
     * Retunrs $default when does not exists in the container.
     *
     * @param mixed|null $default
     */
    public static function get(string $id, $default = null)
    {
        return static::$container[$id] ?? $default;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     */
    public static function has(string $id): bool
    {
        return isset(static::$container[$id]);
    }

    /**
     * Returns the container.
     */
    public static function list(): array
    {
        return static::$container;
    }

    /**
     * Clear the container.
     */
    public static function clear(): void
    {
        static::$container = [];
    }
}
