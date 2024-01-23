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
use Phalcon\Logger\Exception;
use Phalcon\Logger\Formatter\Json;
use Phalcon\Proxy\Psr3\Logger;
use Phalcon\Proxy\Psr3\Tests\Support\Traits\SupportTrait;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

use function date;
use function file_get_contents;
use function sprintf;
use function time;

use const PHP_EOL;

final class ConstructTest extends TestCase
{
    use SupportTrait;

    /**
     * Tests Phalcon\Logger :: __construct() - implement PSR
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerConstructImplementPsr()
    {
        $logger = new Logger('my-logger');
        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    /**
     * Tests Phalcon\Logger :: __construct() - constants
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerConstructConstants()
    {
        $this->assertSame(2, Logger::ALERT);
        $this->assertSame(1, Logger::CRITICAL);
        $this->assertSame(7, Logger::DEBUG);
        $this->assertSame(0, Logger::EMERGENCY);
        $this->assertSame(3, Logger::ERROR);
        $this->assertSame(6, Logger::INFO);
        $this->assertSame(5, Logger::NOTICE);
        $this->assertSame(4, Logger::WARNING);
        $this->assertSame(8, Logger::CUSTOM);
    }

    /**
     * Tests Phalcon\Logger :: __construct() - file with json formatter
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerConstructStreamWithJsonConstants()
    {
        $fileName   = $this->getNewFileName('log');
        $outputPath = $this->getLogsDirectory();
        $adapter    = new Stream($outputPath . $fileName);

        $adapter->setFormatter(new Json());

        $logger = new Logger(
            'my-logger',
            [
                'one' => $adapter,
            ]
        );

        $time = time();

        $logger->debug('This is a message');
        $logger->log(Logger::ERROR, 'This is an error');
        $logger->error('This is another error');

        $contents = file_get_contents($outputPath . $fileName);

        $expected = sprintf(
            '{"level":"DEBUG","message":"This is a message","timestamp":"%s"}' . PHP_EOL .
            '{"level":"ERROR","message":"This is an error","timestamp":"%s"}' . PHP_EOL .
            '{"level":"ERROR","message":"This is another error","timestamp":"%s"}',
            date('c', $time),
            date('c', $time),
            date('c', $time)
        );

        $this->assertStringContainsString($expected, $contents);

        $adapter->close();
        $this->safeDeleteFile($outputPath . $fileName);
    }

    /**
     * Tests Phalcon\Logger :: __construct() - read only mode exception
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerConstructStreamReadOnlyModeException()
    {
        $fileName   = $this->getNewFileName('log');
        $outputPath = $this->getLogsDirectory();
        $file       = $outputPath . $fileName;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Adapter cannot be opened in read mode');

        $adapter = new Stream($file, ['mode' => 'r',]);
    }

    /**
     * Tests Phalcon\Logger :: __construct() - no adapter exception
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerConstructNoAdapterException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No adapters specified');

        $logger = new Logger('my-logger');
        $logger->info('Some message');
    }
}
