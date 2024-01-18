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
use Phalcon\Proxy\Psr3\Tests\Support\Traits\SupportTrait;
use PHPUnit\Framework\TestCase;

use function file_get_contents;

final class AddAdapterTest extends TestCase
{
    use SupportTrait;

    /**
     * Tests Phalcon\Logger :: addAdapter()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerAddAdapter()
    {
        $fileName1  = $this->getNewFileName('log');
        $fileName2  = $this->getNewFileName('log');
        $outputPath = $this->getLogsDirectory();
        $adapter1   = new Stream($outputPath . $fileName1);
        $adapter2   = new Stream($outputPath . $fileName2);

        $logger = new Logger(
            'my-logger',
            [
                'one' => $adapter1,
            ]
        );

        $expected = 1;
        $actual   = $logger->getAdapters();
        $this->assertCount($expected, $actual);

        $logger->addAdapter('two', $adapter2);
        $expected = 2;
        $actual   = $logger->getAdapters();
        $this->assertCount($expected, $actual);

        $logger->debug('Hello');

        $adapter1->close();
        $adapter2->close();

        $contents = file_get_contents($outputPath . $fileName1);
        $this->assertStringContainsString('[DEBUG] Hello', $contents);

        $contents = file_get_contents($outputPath . $fileName2);
        $this->assertStringContainsString('Hello', $contents);

        $this->safeDeleteFile($outputPath . $fileName1);
        $this->safeDeleteFile($outputPath . $fileName2);
    }
}
