<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace PersiLiao\Contract;

interface NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param mixed $object
     *
     * @return array|\ArrayObject|bool|float|int|string|null
     */
    public function normalize($object);

    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data  Data to restore
     * @param string $class The expected class to instantiate
     *
     * @return mixed|object
     */
    public function denormalize($data, string $class);
}
