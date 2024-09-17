<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Processor;

use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightUserUpdaterInterface;

class AssetBundleQuicksightUserProcessor implements AssetBundleQuicksightUserProcessorInterface
{
    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::QUICKSIGHT_USER_ROLE_AUTHOR
     *
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_AUTHOR = 'AUTHOR';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::QUICKSIGHT_USER_ROLE_READER
     *
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_READER = 'READER';

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreatorInterface
     */
    protected QuicksightUserCreatorInterface $quicksightUserCreator;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightUserUpdaterInterface
     */
    protected QuicksightUserUpdaterInterface $quicksightUserUpdater;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreatorInterface $quicksightUserCreator
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightUserUpdaterInterface $quicksightUserUpdater
     */
    public function __construct(
        QuicksightUserCreatorInterface $quicksightUserCreator,
        QuicksightUserUpdaterInterface $quicksightUserUpdater
    ) {
        $this->quicksightUserCreator = $quicksightUserCreator;
        $this->quicksightUserUpdater = $quicksightUserUpdater;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    public function processQuicksightUserBeforeAnalyticsEnabling(
        UserTransfer $userTransfer
    ): UserCollectionResponseTransfer {
        $quicksightUserTransfer = $userTransfer->getQuicksightUser();

        if (!$quicksightUserTransfer) {
            return $this->quicksightUserCreator->createQuicksightUsersByUserCollectionResponse(
                (new UserCollectionResponseTransfer())->addUser($userTransfer
                    ->setQuicksightUser((new QuicksightUserTransfer())->setRole(static::QUICKSIGHT_USER_ROLE_AUTHOR))),
            );
        }

        if ($quicksightUserTransfer->getRole() === static::QUICKSIGHT_USER_ROLE_READER) {
            $userTransfer->getQuicksightUserOrFail()->setRole(static::QUICKSIGHT_USER_ROLE_AUTHOR);
            $quicksightUpdateUserResponseTransfer = $this->quicksightUserUpdater->updateQuicksightUser($userTransfer);
            $userTransfer->setQuicksightUser($quicksightUpdateUserResponseTransfer->getQuicksightUserOrFail());

            return (new UserCollectionResponseTransfer())
                ->addUser($userTransfer)
                ->setErrors($quicksightUpdateUserResponseTransfer->getErrors());
        }

        return (new UserCollectionResponseTransfer())->addUser($userTransfer);
    }
}
