<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight;

use Codeception\Actor;
use Generated\Shared\DataBuilder\QuicksightUserBuilder;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade(?string $moduleName = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class AmazonQuicksightBusinessTester extends Actor
{
    use _generated\AmazonQuicksightBusinessTesterActions;

    /**
     * @param string $quicksightUserRole
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function haveUserWithNotPersistedQuicksightUserRole(string $quicksightUserRole): UserTransfer
    {
        return $this->haveUser([
            UserTransfer::QUICKSIGHT_USER => (new QuicksightUserBuilder([
                QuicksightUserTransfer::ROLE => $quicksightUserRole,
            ]))->build(),
        ]);
    }
}
