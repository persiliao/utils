<?php
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 *
 * @see https://www.github.com/persiliao
 */

declare(strict_types=1);

namespace PersiLiao\Utils;

use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;

class DotEnv
{
    public static function load(string $paths): void
    {
        $repository = RepositoryBuilder::createWithNoAdapters()->addAdapter(EnvConstAdapter::class)->addWriter(PutenvAdapter::class)->immutable()->make();
        \Dotenv\Dotenv::create($repository, $paths)->load();
    }
}
