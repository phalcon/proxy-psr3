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

namespace Phalcon\Proxy\Psr3\Tests\Unit\Logger\Adapter\Noop;

use Phalcon\Logger\Adapter\Noop;
use Phalcon\Logger\Exception;
use UnitTester;

class CommitCest
{
    /**
     * Tests Phalcon\Logger\Adapter\Noop :: commit()
     *
     * @param UnitTester $I
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function loggerAdapterNoopCommit(UnitTester $I)
    {
        $I->wantToTest('Logger\Adapter\Noop - commit()');

        $adapter = new Noop();

        $adapter->begin();

        $I->assertTrue(
            $adapter->inTransaction()
        );

        $adapter->commit();

        $I->assertFalse(
            $adapter->inTransaction()
        );
    }

    /**
     * Tests Phalcon\Logger\Adapter\Noop :: commit() - no transaction
     *
     * @param UnitTester $I
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function loggerAdapterNoopCommitNoTransaction(UnitTester $I)
    {
        $I->wantToTest('Logger\Adapter\Noop - commit() - no transaction');

        $adapter = new Noop();

        $I->assertFalse(
            $adapter->inTransaction()
        );

        $I->expectThrowable(
            new Exception('There is no active transaction'),
            function () use ($adapter) {
                $adapter->commit();
            }
        );
    }
}
