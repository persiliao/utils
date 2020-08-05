<?php

declare(strict_types=1);
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 * @link https://www.github.com/persiliao
 */

namespace PersiLiao\Utils;

use Psr\Container\ContainerInterface;

class ApplicationContext
{
    /**
     * @var null|ContainerInterface
     */
    private static $container;

    /**
     * @throws \TypeError
     */
    public static function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    public static function setContainer(ContainerInterface $container): ContainerInterface
    {
        self::$container = $container;
        return $container;
    }

    public static function hasContainer(): bool
    {
        return isset(self::$container);
    }
}
