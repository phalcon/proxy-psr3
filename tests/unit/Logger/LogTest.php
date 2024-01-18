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

namespace Phalcon\Proxy\Psr3\Tests\Unit\Logger;

use Phalcon\Logger\Adapter\Stream;
use Phalcon\Proxy\Psr3\Logger;
use PHPUnit\Framework\TestCase;

use function logsDir;
use function sprintf;
use function strtoupper;

final class LogTest extends TestCase
{
    /**
     * Tests Phalcon\Logger :: log()
     *
     * @param
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerLog()
    {
        $I->wantToTest('Logger - log()');

        $logPath  = logsDir();
        $fileName = $I->getNewFileName('log');
        $adapter  = new Stream($logPath . $fileName);

        $logger = new Logger(
            'my-logger',
            [
                'one' => $adapter,
            ]
        );

        $levels = [
            Logger::ALERT     => 'alert',
            Logger::CRITICAL  => 'critical',
            Logger::DEBUG     => 'debug',
            Logger::EMERGENCY => 'emergency',
            Logger::ERROR     => 'error',
            Logger::INFO      => 'info',
            Logger::NOTICE    => 'notice',
            Logger::WARNING   => 'warning',
            Logger::CUSTOM    => 'custom',
            'alert'           => 'alert',
            'critical'        => 'critical',
            'debug'           => 'debug',
            'emergency'       => 'emergency',
            'error'           => 'error',
            'info'            => 'info',
            'notice'          => 'notice',
            'warning'         => 'warning',
            'custom'          => 'custom',
        ];

        foreach ($levels as $level => $levelName) {
            $logger->log($level, 'Message ' . $levelName);
        }

        $I->amInPath($logPath);
        $I->openFile($fileName);

        foreach ($levels as $levelName) {
            $expected = sprintf(
                '[%s] Message %s',
                strtoupper($levelName),
                $levelName
            );

            $I->seeInThisFile($expected);
        }

        $adapter->close();
        $I->safeDeleteFile($fileName);
    }

    /**
     * Tests Phalcon\Logger :: log() - logLevel
     *
     * @param
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerLogLogLevel()
    {
        $I->wantToTest('Logger - log() - logLevel');

        $logPath  = logsDir();
        $fileName = $I->getNewFileName('log');
        $adapter  = new Stream($logPath . $fileName);

        $logger = new Logger(
            'my-logger',
            [
                'one' => $adapter,
            ]
        );

        $logger->setLogLevel(Logger::ALERT);

        $levelsYes = [
            Logger::ALERT     => 'alert',
            Logger::CRITICAL  => 'critical',
            Logger::EMERGENCY => 'emergency',
            'alert'           => 'alert',
            'critical'        => 'critical',
            'emergency'       => 'emergency',
        ];

        $levelsNo = [
            Logger::DEBUG   => 'debug',
            Logger::ERROR   => 'error',
            Logger::INFO    => 'info',
            Logger::NOTICE  => 'notice',
            Logger::WARNING => 'warning',
            Logger::CUSTOM  => 'custom',
            'debug'         => 'debug',
            'error'         => 'error',
            'info'          => 'info',
            'notice'        => 'notice',
            'warning'       => 'warning',
            'custom'        => 'custom',
        ];

        foreach ($levelsYes as $level => $levelName) {
            $logger->log($level, 'Message ' . $levelName);
        }

        foreach ($levelsNo as $level => $levelName) {
            $logger->log($level, 'Message ' . $levelName);
        }

        $I->amInPath($logPath);
        $I->openFile($fileName);

        foreach ($levelsYes as $levelName) {
            $expected = sprintf(
                '[%s] Message %s',
                strtoupper($levelName),
                $levelName
            );
            $I->seeInThisFile($expected);
        }

        foreach ($levelsNo as $levelName) {
            $expected = sprintf(
                '[%s] Message %s',
                strtoupper($levelName),
                $levelName
            );
            $I->dontSeeInThisFile($expected);
        }

        $adapter->close();
        $I->safeDeleteFile($fileName);
    }
}
