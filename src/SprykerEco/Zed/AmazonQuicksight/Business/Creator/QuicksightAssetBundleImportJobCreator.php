<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Creator;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightAssetBundleImportJobCreator implements QuicksightAssetBundleImportJobCreatorInterface
{
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
        AmazonQuicksightConfig $amazonQuicksightConfig,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
    ) {
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->amazonQuicksightEntityManager = $amazonQuicksightEntityManager;
    }

    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function createDefaultAssetBundleImportJobByEnableQuicksightAnalyticsRequest(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        return $this->amazonQuicksightEntityManager->createQuicksightAssetBundleImportJob(
            (new QuicksightAssetBundleImportJobTransfer())
                ->setStatus($this->amazonQuicksightConfig->getDefaultNewAssetBundleImportJobStatus())
                ->setJobId($enableQuicksightAnalyticsRequestTransfer->getAssetBundleImportJobId()),
        );
    }
}
