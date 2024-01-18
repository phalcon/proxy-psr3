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

namespace Phalcon\Proxy\Psr3\Tests\Unit\Logger\Logger;

use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\Exception;
use Phalcon\Proxy\Psr3\Logger;
use PHPUnit\Framework\TestCase;

use function logsDir;

final class GetAdapterTest extends TestCase
{
    /**
     * Tests Phalcon\Logger :: getAdapter()
     *
     * @param
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerGetAdapter()
    {
        $I->wantToTest('Logger - getAdapter()');
        $fileName1  = $I->getNewFileName('log', 'log');
        $outputPath = logsDir();
        $adapter1   = new Stream($outputPath . $fileName1);

        $logger = new Logger(
            'my-logger',
            [
                'one' => $adapter1,
            ]
        );


        $class  = Stream::class;
        $actual = $logger->getAdapter('one');
        $this->assertInstanceOf($class, $actual);

        $adapter1->close();
        $I->safeDeleteFile($outputPath . $fileName1);
    }

    /**
     * Tests Phalcon\Logger :: getAdapter() - unknown
     *
     * @param
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerGetAdapterUnknown()
    {
        $I->wantToTest('Logger - getAdapter() - unknown');

        $I->expectThrowable(
            new Exception('Adapter does not exist for this logger'),
            function () {
                $logger = new Logger('my-logger');
                $logger->getAdapter('unknown');
            }
        );
    }

    /**
     * Tests Phalcon\Logger :: getAdapter() - for transaction
     *
     * @param
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerGetAdapterForTransaction()
    {
        $I->wantToTest('Logger - getAdapter() - for transaction');
        $fileName1  = $I->getNewFileName('log', 'log');
        $fileName2  = $I->getNewFileName('log', 'log');
        $outputPath = logsDir();

        $adapter1 = new Stream($outputPath . $fileName1);
        $adapter2 = new Stream($outputPath . $fileName2);

        $logger = new Logger(
            'my-logger',
            [
                'one' => $adapter1,
                'two' => $adapter2,
            ]
        );

        $logger->info('Logging');

        $logger->getAdapter('two')
               ->begin()
        ;

        $this->assertFalse(
            $logger->getAdapter('one')
                   ->inTransaction()
        );
        $this->assertTrue(
            $logger->getAdapter('two')
                   ->inTransaction()
        );

        $logger->info('Thanks');
        $logger->info('for');
        $logger->info('Phlying');
        $logger->info('with');
        $logger->info('Phalcon');

        $I->amInPath($outputPath);
        $I->openFile($fileName1);
        $I->seeInThisFile('Logging');
        $I->seeInThisFile('Thanks');
        $I->seeInThisFile('for');
        $I->seeInThisFile('Phlying');
        $I->seeInThisFile('with');
        $I->seeInThisFile('Phalcon');

        $I->amInPath($outputPath);
        $I->openFile($fileName2);
        $I->dontSeeInThisFile('Thanks');
        $I->dontSeeInThisFile('for');
        $I->dontSeeInThisFile('Phlying');
        $I->dontSeeInThisFile('with');
        $I->dontSeeInThisFile('Phalcon');

        $logger->getAdapter('two')
               ->commit()
        ;

        $I->amInPath($outputPath);
        $I->openFile($fileName2);
        $I->seeInThisFile('Thanks');
        $I->seeInThisFile('for');
        $I->seeInThisFile('Phlying');
        $I->seeInThisFile('with');
        $I->seeInThisFile('Phalcon');

        $adapter1->close();
        $adapter2->close();

        $I->safeDeleteFile($outputPath . $fileName1);
        $I->safeDeleteFile($outputPath . $fileName2);
    }
}
