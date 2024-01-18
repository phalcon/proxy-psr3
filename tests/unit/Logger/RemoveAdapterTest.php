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
use Phalcon\Proxy\Psr3\Logger;
use PHPUnit\Framework\TestCase;

final class RemoveAdapterTest extends TestCase
{
    /**
     * Tests Phalcon\Logger :: removeAdapter()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerRemoveAdapter()
    {
        $I->wantToTest('Logger - removeAdapter()');

        $fileName1  = $I->getNewFileName('log');
        $fileName2  = $I->getNewFileName('log');
        $outputPath = logsDir();
        $adapter1   = new Stream($outputPath . $fileName1);
        $adapter2   = new Stream($outputPath . $fileName2);

        $logger = new Logger(
            'my-logger',
            [
                'one' => $adapter1,
                'two' => $adapter2,
            ]
        );

        $expected = 2;
        $adapters = $logger->getAdapters();
        $this->assertCount($expected, $adapters);

        $logger->removeAdapter('one');
        $expected = 1;
        $adapters = $logger->getAdapters();
        $this->assertCount($expected, $adapters);

        $I->safeDeleteFile($outputPath . $fileName1);
        $I->safeDeleteFile($outputPath . $fileName2);
    }

    /**
     * Tests Phalcon\Logger :: removeAdapter() - unknown
     *
     * @param
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerRemoveAdapterUnknown()
    {
        $I->wantToTest('Logger - removeAdapter() - unknown');

        $fileName1  = $I->getNewFileName('log');
        $outputPath = logsDir();

        try {
            $adapter1 = new Stream($outputPath . $fileName1);

            $logger = new Logger(
                'my-logger',
                [
                    'one' => $adapter1,
                ]
            );

            $expected = 1;
            $adapters = $logger->getAdapters();
            $this->assertCount($expected, $adapters);

            $logger->removeAdapter('unknown');
        } catch (Exception $ex) {
            $expected = 'Adapter does not exist for this logger';
            $actual   = $ex->getMessage();
            $this->assertEquals($expected, $actual);
        }

        $I->safeDeleteFile($outputPath . $fileName1);
    }
}
