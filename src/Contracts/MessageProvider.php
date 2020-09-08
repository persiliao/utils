<?php

declare(strict_types=1);
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 *
 * @see https://www.github.com/persiliao
 */

namespace PersiLiao\Utils\Contracts;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     */
    public function getMessageBag(): MessageBag;
}
