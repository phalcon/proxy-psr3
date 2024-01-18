<?php

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Proxy\Psr3;

use Phalcon\Logger\AbstractLogger;
use Phalcon\Logger\Adapter\AdapterInterface;
use Phalcon\Logger\Exception as LoggerException;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Phalcon Proxy PSR-3.
 *
 * A PSR compatible proxy class utilizing the Phalcon\Logger.
 *
 * @property AdapterInterface[] $adapters
 * @property array              $excluded
 * @property int                $logLevel
 * @property string             $name
 * @property string             $timezone
 */
class Logger extends AbstractLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed   $level
     * @param string|\Stringable $message
     * @param mixed[] $context
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $intLevel = $this->getLevelNumber($level);

        $this->addMessage($intLevel, (string) $message, $context);
    }
}
