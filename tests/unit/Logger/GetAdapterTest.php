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
use Phalcon\Proxy\Psr3\Tests\Support\Traits\SupportTrait;
use PHPUnit\Framework\TestCase;

use function file_get_contents;

final class GetAdapterTest extends TestCase
{
    use SupportTrait;

    /**
     * Tests Phalcon\Logger :: getAdapter()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerGetAdapter()
    {
        $fileName1  = $this->getNewFileName('log');
        $outputPath = $this->getLogsDirectory();
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
        $this->safeDeleteFile($outputPath . $fileName1);
    }

    /**
     * Tests Phalcon\Logger :: getAdapter() - unknown
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerGetAdapterUnknown()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Adapter does not exist for this logger');

        $logger = new Logger('my-logger');
        $logger->getAdapter('unknown');
    }

    /**
     * Tests Phalcon\Logger :: getAdapter() - for transaction
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function testLoggerGetAdapterForTransaction()
    {
        $fileName1  = $this->getNewFileName('log');
        $fileName2  = $this->getNewFileName('log');
        $outputPath = $this->getLogsDirectory();

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

        $contents = file_get_contents($outputPath . $fileName1);
        $this->assertStringContainsString('Logging', $contents);
        $this->assertStringContainsString('Thanks', $contents);
        $this->assertStringContainsString('for', $contents);
        $this->assertStringContainsString('Phlying', $contents);
        $this->assertStringContainsString('with', $contents);
        $this->assertStringContainsString('Phalcon', $contents);

        $contents = file_get_contents($outputPath . $fileName2);
        $this->assertStringNotContainsString('Thanks', $contents);
        $this->assertStringNotContainsString('for', $contents);
        $this->assertStringNotContainsString('Phlying', $contents);
        $this->assertStringNotContainsString('with', $contents);
        $this->assertStringNotContainsString('Phalcon', $contents);

        $logger->getAdapter('two')
               ->commit()
        ;

        $contents = file_get_contents($outputPath . $fileName2);
        $this->assertStringContainsString('Thanks', $contents);
        $this->assertStringContainsString('for', $contents);
        $this->assertStringContainsString('Phlying', $contents);
        $this->assertStringContainsString('with', $contents);
        $this->assertStringContainsString('Phalcon', $contents);

        $adapter1->close();
        $adapter2->close();

        $this->safeDeleteFile($outputPath . $fileName1);
        $this->safeDeleteFile($outputPath . $fileName2);
    }
}
