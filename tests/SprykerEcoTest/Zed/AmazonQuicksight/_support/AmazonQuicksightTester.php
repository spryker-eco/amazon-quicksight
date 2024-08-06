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
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser;

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
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 *
 * @SuppressWarnings(PHPMD)
 */
class AmazonQuicksightTester extends Actor
{
    use _generated\AmazonQuicksightTesterActions;

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function haveQuicksightUser(UserTransfer $userTransfer): QuicksightUserTransfer
    {
        $quicksightUserTransfer = (new QuicksightUserBuilder())->build();
        $quicksightUserTransfer->setFkUser($userTransfer->getIdUserOrFail());
        $quicksightUserEntity = (new SpyQuicksightUser())
            ->fromArray($quicksightUserTransfer->toArray());
        $quicksightUserEntity->save();

        $quicksightUserTransfer->setFkUser($quicksightUserEntity->getFkUser());

        return $quicksightUserTransfer;
    }
}
