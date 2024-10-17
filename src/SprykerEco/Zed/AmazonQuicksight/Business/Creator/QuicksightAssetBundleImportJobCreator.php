<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Creator;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdaterInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightAssetBundleImportJobCreator implements QuicksightAssetBundleImportJobCreatorInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface
     */
    protected AssetBundleAmazonQuicksightApiClientInterface $assetBundleAmazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface
     */
    protected AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdaterInterface
     */
    protected QuicksightAssetBundleImportJobUpdaterInterface $quicksightAssetBundleImportJobUpdater;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface $assetBundleAmazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdaterInterface $quicksightAssetBundleImportJobUpdater
     */
    public function __construct(
        AssetBundleAmazonQuicksightApiClientInterface $assetBundleAmazonQuicksightApiClient,
        AmazonQuicksightConfig $amazonQuicksightConfig,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager,
        QuicksightAssetBundleImportJobUpdaterInterface $quicksightAssetBundleImportJobUpdater
    ) {
        $this->assetBundleAmazonQuicksightApiClient = $assetBundleAmazonQuicksightApiClient;
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->amazonQuicksightEntityManager = $amazonQuicksightEntityManager;
        $this->quicksightAssetBundleImportJobUpdater = $quicksightAssetBundleImportJobUpdater;
    }

    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer
     */
    public function createDefaultAssetBundleImportJobByEnableQuicksightAnalyticsRequest(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): EnableQuicksightAnalyticsResponseTransfer {
        $enableQuicksightAnalyticsResponseTransfer = new EnableQuicksightAnalyticsResponseTransfer();

        $quicksightStartAssetBundleImportJobResponseTransfer = $this->assetBundleAmazonQuicksightApiClient
            ->startAssetBundleImportJobByEnableQuicksightAnalyticsRequest($enableQuicksightAnalyticsRequestTransfer);

        if ($quicksightStartAssetBundleImportJobResponseTransfer->getErrors()->count() !== 0) {
            return $enableQuicksightAnalyticsResponseTransfer->setErrors(
                $quicksightStartAssetBundleImportJobResponseTransfer->getErrors(),
            );
        }

        $quicksightAssetBundleImportJobTransfer = $this->saveNewQuicksightAssetBundleImportJob($enableQuicksightAnalyticsRequestTransfer);

        return $enableQuicksightAnalyticsResponseTransfer
            ->setQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    protected function saveNewQuicksightAssetBundleImportJob(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        $quicksightAssetBundleImportJobTransfer = $enableQuicksightAnalyticsRequestTransfer->getQuicksightAssetBundleImportJob();

        if ($quicksightAssetBundleImportJobTransfer) {
            return $this->quicksightAssetBundleImportJobUpdater
                ->resetDefaultQuicksightAssetBundleImportJobInDb($quicksightAssetBundleImportJobTransfer);
        }

        return $this->createDefaultAssetBundleImportJobInDb($enableQuicksightAnalyticsRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    protected function createDefaultAssetBundleImportJobInDb(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        return $this->amazonQuicksightEntityManager->createQuicksightAssetBundleImportJob(
            (new QuicksightAssetBundleImportJobTransfer())
                ->setStatus($this->amazonQuicksightConfig->getDefaultNewAssetBundleImportJobStatus())
                ->setJobId($enableQuicksightAnalyticsRequestTransfer->getAssetBundleImportJobId()),
        );
    }
}
