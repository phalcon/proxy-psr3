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

final class ExcludeAdaptersTest extends TestCase
{
    use SupportTrait;

    /**
     * Tests Phalcon\Logger :: excludeAdapters()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerExcludeAdapters()
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
                'two' => $adapter2,
            ]
        );

        /**
         * Log into both
         */
        $logger->debug('Hello');

        $contents = file_get_contents($outputPath . $fileName1);
        $this->assertStringContainsString('Hello', $contents);

        $contents = file_get_contents($outputPath . $fileName2);
        $this->assertStringContainsString('Hello', $contents);

        /**
         * Exclude a logger
         */
        $logger
            ->excludeAdapters(['two'])
            ->debug('Goodbye')
        ;

        $contents = file_get_contents($outputPath . $fileName1);
        $this->assertStringContainsString('Goodbye', $contents);

        $contents = file_get_contents($outputPath . $fileName2);
        $this->assertStringNotContainsString('Goodbye', $contents);

        $adapter1->close();
        $adapter2->close();

        $this->safeDeleteFile($outputPath . $fileName1);
        $this->safeDeleteFile($outputPath . $fileName2);
    }
}
