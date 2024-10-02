<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Updater;

use Generated\Shared\Transfer\QuicksightUpdateUserResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightUserUpdater implements QuicksightUserUpdaterInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface
     */
    protected AmazonQuicksightApiClientInterface $amazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface
     */
    protected AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface $amazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     */
    public function __construct(
        AmazonQuicksightApiClientInterface $amazonQuicksightApiClient,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
    ) {
        $this->amazonQuicksightApiClient = $amazonQuicksightApiClient;
        $this->amazonQuicksightEntityManager = $amazonQuicksightEntityManager;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUpdateUserResponseTransfer
     */
    public function updateQuicksightUser(UserTransfer $userTransfer): QuicksightUpdateUserResponseTransfer
    {
        $quicksightUserTransfer = $userTransfer->getQuicksightUserOrFail();
        $quicksightUpdateUserResponseTransfer = $this->amazonQuicksightApiClient->updateUser($userTransfer);

        if ($quicksightUpdateUserResponseTransfer->getErrors()->count() !== 0) {
            return $quicksightUpdateUserResponseTransfer->setQuicksightUser($quicksightUserTransfer);
        }

        $quicksightUserTransfer = $this->amazonQuicksightEntityManager->updateQuicksightUser($quicksightUserTransfer);

        return $quicksightUpdateUserResponseTransfer->setQuicksightUser($quicksightUserTransfer);
    }
}
