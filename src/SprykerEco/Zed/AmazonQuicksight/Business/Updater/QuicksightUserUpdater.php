<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Updater;

use Generated\Shared\Transfer\QuicksightUpdateUserResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightUserUpdater implements QuicksightUserUpdaterInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface
     */
    protected UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface
     */
    protected AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     */
    public function __construct(
        UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
    ) {
        $this->userAmazonQuicksightApiClient = $userAmazonQuicksightApiClient;
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
        $quicksightUpdateUserResponseTransfer = $this->userAmazonQuicksightApiClient->updateUser($userTransfer);

        if ($quicksightUpdateUserResponseTransfer->getErrors()->count() !== 0) {
            return $quicksightUpdateUserResponseTransfer->setQuicksightUser($quicksightUserTransfer);
        }

        $quicksightUserTransfer = $this->amazonQuicksightEntityManager->updateQuicksightUser($quicksightUserTransfer);

        return $quicksightUpdateUserResponseTransfer->setQuicksightUser($quicksightUserTransfer);
    }
}
