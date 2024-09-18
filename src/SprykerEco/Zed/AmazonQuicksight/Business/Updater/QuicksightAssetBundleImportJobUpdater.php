<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Updater;

use ArrayObject;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightAssetBundleImportJobUpdater implements QuicksightAssetBundleImportJobUpdaterInterface
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
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function resetDefaultQuicksightAssetBundleImportJob(
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        $quicksightAssetBundleImportJobTransfer->setStatus($this->amazonQuicksightConfig->getDefaultNewAssetBundleImportJobStatus());
        $quicksightAssetBundleImportJobTransfer->setErrors(new ArrayObject());
        $quicksightAssetBundleImportJobTransfer = $this->amazonQuicksightEntityManager
            ->updateQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer);

        return $quicksightAssetBundleImportJobTransfer;
    }
}
