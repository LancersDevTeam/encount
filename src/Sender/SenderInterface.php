<?php
declare(strict_types=1);

namespace Encount\Sender;

use Encount\Collector\EncountCollector;

interface SenderInterface
{
    public function send($config, EncountCollector $collector);
}
