<?php

declare(strict_types=1);
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 *
 * @see https://www.github.com/persiliao
 */

namespace PersiLiao\Utils\Serializer;

use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerFactory
{
    /**
     * @var string
     */
    protected $serializer;

    public function __construct(string $serializer = Serializer::class)
    {
        $this->serializer = $serializer;
    }

    public function __invoke()
    {
        return new $this->serializer([
            //new ExceptionNormalizer(),
            new ObjectNormalizer(),
            new ArrayDenormalizer(),
            new ScalarNormalizer(),
        ]);
    }
}
