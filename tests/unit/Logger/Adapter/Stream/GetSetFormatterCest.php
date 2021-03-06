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

namespace Phalcon\Proxy\Psr3\Tests\Unit\Logger\Adapter\Stream;

use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\Formatter\FormatterInterface;
use Phalcon\Logger\Formatter\Line;
use UnitTester;

class GetSetFormatterCest
{
    /**
     * Tests Phalcon\Logger\Adapter\Stream :: getFormatter()/setFormatter()
     */
    public function loggerAdapterStreamGetSetFormatter(UnitTester $I)
    {
        $I->wantToTest('Logger\Adapter\Stream - getFormatter()/setFormatter()');

        $fileName = $I->getNewFileName('log', 'log');
        $fileName = logsDir($fileName);

        $adapter = new Stream($fileName);

        $adapter->setFormatter(new Line());
        $actual = $adapter->getFormatter();

        $I->assertInstanceOf(FormatterInterface::class, $actual);

        $adapter->close();
        $I->safeDeleteFile($fileName);
    }
}
