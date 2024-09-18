<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Updater;

use ArrayObject;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightAssetBundleImportJobUpdater implements QuicksightAssetBundleImportJobUpdaterInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface
     */
    protected AmazonQuicksightApiClientInterface $amazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface
     */
    protected AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     */
    public function __construct(
        AmazonQuicksightApiClientInterface $amazonQuicksightApiClient,
        AmazonQuicksightConfig $amazonQuicksightConfig,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
    ) {
        $this->amazonQuicksightApiClient = $amazonQuicksightApiClient;
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->amazonQuicksightEntityManager = $amazonQuicksightEntityManager;
    }

    /**
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer
     */
    public function resetDefaultQuicksightAssetBundleImportJob(
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
    ): ResetQuicksightAnalyticsResponseTransfer {
        $resetQuicksightAnalyticsResponseTransfer = new ResetQuicksightAnalyticsResponseTransfer();

        $quicksightStartAssetBundleImportJobResponseTransfer = $this->amazonQuicksightApiClient
            ->startAssetBundleImportJobByResetQuicksightAnalyticsRequest($resetQuicksightAnalyticsRequestTransfer);

        if ($quicksightStartAssetBundleImportJobResponseTransfer->getErrors()->count() !== 0) {
            return $resetQuicksightAnalyticsResponseTransfer->setErrors(
                $quicksightStartAssetBundleImportJobResponseTransfer->getErrors(),
            );
        }

        $quicksightAssetBundleImportJobTransfer = $this->resetDefaultQuicksightAssetBundleImportJobInDb(
            $resetQuicksightAnalyticsRequestTransfer->getQuicksightAssetBundleImportJobOrFail(),
        );

        return $resetQuicksightAnalyticsResponseTransfer
            ->setQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function resetDefaultQuicksightAssetBundleImportJobInDb(
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        $quicksightAssetBundleImportJobTransfer->setStatus($this->amazonQuicksightConfig->getDefaultNewAssetBundleImportJobStatus());
        $quicksightAssetBundleImportJobTransfer->setErrors(new ArrayObject());
        $quicksightAssetBundleImportJobTransfer = $this->amazonQuicksightEntityManager
            ->updateQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer);

        return $quicksightAssetBundleImportJobTransfer;
    }
}
