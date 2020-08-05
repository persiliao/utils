<?php

declare(strict_types=1);
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 * @link https://www.github.com/persiliao
 */

namespace PersiLiao\Utils\Codec;

use InvalidArgumentException;
use PersiLiao\Utils\Contracts\Arrayable;
use PersiLiao\Utils\Contracts\Jsonable;

class Json
{
    public static function encode($data, $options = JSON_UNESCAPED_UNICODE): string
    {
        if($data instanceof Jsonable){
            return (string)$data;
        }

        if($data instanceof Arrayable){
            $data = $data->toArray();
        }

        $json = json_encode($data, $options);

        static::handleJsonError(json_last_error(), json_last_error_msg());

        return $json;
    }

    protected static function handleJsonError($lastError, $message)
    {
        if($lastError === JSON_ERROR_NONE){
            return;
        }

        throw new InvalidArgumentException($message, $lastError);
    }

    public static function decode(string $json, $assoc = true)
    {
        $decode = json_decode($json, $assoc);

        static::handleJsonError(json_last_error(), json_last_error_msg());

        return $decode;
    }
}
