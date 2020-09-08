<?php

declare(strict_types=1);
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 *
 * @see https://www.github.com/persiliao
 */

namespace PersiLiao\Utils\Serializer;

use PersiLiao\Contract\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class SymfonyNormalizer implements NormalizerInterface
{
    /**
     * @var Serializer
     */
    protected $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function normalize($object)
    {
        return $this->serializer->normalize($object);
    }

    public function denormalize($data, string $class)
    {
        return $this->serializer->denormalize($data, $class);
    }
}
