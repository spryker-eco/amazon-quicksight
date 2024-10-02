<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Processor;

use Generated\Shared\Transfer\QuicksightUserConditionsTransfer;
use Generated\Shared\Transfer\QuicksightUserCriteriaTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightUserUpdaterInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;

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
     * @var list<string>
     */
    protected const QUICKSIGHT_USER_ROLES_TO_UPDATE_TO_AUTHOR = [
        self::QUICKSIGHT_USER_ROLE_READER,
    ];

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreatorInterface
     */
    protected QuicksightUserCreatorInterface $quicksightUserCreator;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightUserUpdaterInterface
     */
    protected QuicksightUserUpdaterInterface $quicksightUserUpdater;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface
     */
    protected AmazonQuicksightRepositoryInterface $amazonQuicksightRepository;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreatorInterface $quicksightUserCreator
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightUserUpdaterInterface $quicksightUserUpdater
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface $amazonQuicksightRepository
     */
    public function __construct(
        QuicksightUserCreatorInterface $quicksightUserCreator,
        QuicksightUserUpdaterInterface $quicksightUserUpdater,
        AmazonQuicksightRepositoryInterface $amazonQuicksightRepository,
    ) {
        $this->quicksightUserCreator = $quicksightUserCreator;
        $this->quicksightUserUpdater = $quicksightUserUpdater;
        $this->amazonQuicksightRepository = $amazonQuicksightRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    public function processQuicksightUserBeforeAnalyticsEnabling(
        UserTransfer $userTransfer,
    ): UserCollectionResponseTransfer {
        $quicksightUserTransfer = $this->amazonQuicksightRepository->getQuicksightUserCollection(
            (new QuicksightUserCriteriaTransfer())->setQuicksightUserConditions(
                (new QuicksightUserConditionsTransfer())->addIdUser($userTransfer->getIdUserOrFail()),
            ),
        )->getQuicksightUsers()->getIterator()->current();

        if (!$quicksightUserTransfer) {
            return $this->quicksightUserCreator->createQuicksightUsersByUserCollectionResponse(
                (new UserCollectionResponseTransfer())->addUser($userTransfer
                    ->setQuicksightUser((new QuicksightUserTransfer())->setRole(static::QUICKSIGHT_USER_ROLE_AUTHOR))),
            );
        }

        if (in_array($quicksightUserTransfer->getRole(), static::QUICKSIGHT_USER_ROLES_TO_UPDATE_TO_AUTHOR)) {
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
