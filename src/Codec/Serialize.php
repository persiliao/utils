<?php

declare(strict_types=1);
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 * @link https://www.github.com/persiliao
 */

namespace PersiLiao\Utils\Codec;


use function serialize;
use function unserialize;

class Serialize
{
    public static function encode($data)
    {
        return serialize($data);
    }

    public static function decode(string $data, array $options = [ 'allowed_classes' => false ])
    {
        return unserialize($data, $options);
    }
}