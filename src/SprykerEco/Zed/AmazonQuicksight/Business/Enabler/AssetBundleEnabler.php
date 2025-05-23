<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Enabler;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer;
use SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightAssetBundleImportJobCreatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Deleter\DataSetDeleterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdaterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;

class AssetBundleEnabler implements AssetBundleEnablerInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightAssetBundleImportJobCreatorInterface
     */
    protected QuicksightAssetBundleImportJobCreatorInterface $quicksightAssetBundleImportJobCreator;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdaterInterface
     */
    protected QuicksightAssetBundleImportJobUpdaterInterface $quicksightAssetBundleImportJobUpdater;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface
     */
    protected AmazonQuicksightRepositoryInterface $amazonQuicksightRepository;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface
     */
    protected QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface
     */
    protected AssetBundleImportFileContentLoaderInterface $assetBundleImportFileContentLoader;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Deleter\DataSetDeleterInterface
     */
    protected DataSetDeleterInterface $dataSetDeleter;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightAssetBundleImportJobCreatorInterface $quicksightAssetBundleImportJobCreator
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdaterInterface $quicksightAssetBundleImportJobUpdater
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface $amazonQuicksightRepository
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface $assetBundleImportFileContentLoader
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Deleter\DataSetDeleterInterface $dataSetDeleter
     */
    public function __construct(
        QuicksightAssetBundleImportJobCreatorInterface $quicksightAssetBundleImportJobCreator,
        QuicksightAssetBundleImportJobUpdaterInterface $quicksightAssetBundleImportJobUpdater,
        AmazonQuicksightRepositoryInterface $amazonQuicksightRepository,
        QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator,
        AssetBundleImportFileContentLoaderInterface $assetBundleImportFileContentLoader,
        DataSetDeleterInterface $dataSetDeleter
    ) {
        $this->quicksightAssetBundleImportJobCreator = $quicksightAssetBundleImportJobCreator;
        $this->quicksightAssetBundleImportJobUpdater = $quicksightAssetBundleImportJobUpdater;
        $this->amazonQuicksightRepository = $amazonQuicksightRepository;
        $this->quicksightAnalyticsRequestValidator = $quicksightAnalyticsRequestValidator;
        $this->assetBundleImportFileContentLoader = $assetBundleImportFileContentLoader;
        $this->dataSetDeleter = $dataSetDeleter;
    }

    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer
     */
    public function enableAnalytics(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): EnableQuicksightAnalyticsResponseTransfer {
        $enableQuicksightAnalyticsRequestTransfer->requireAssetBundleImportJobId();
        $enableQuicksightAnalyticsRequestTransfer->requireUser();

        $quicksightAssetBundleImportJobTransfer = $this->amazonQuicksightRepository
            ->findQuicksightAssetBundleImportJobByJobId($enableQuicksightAnalyticsRequestTransfer->getAssetBundleImportJobIdOrFail());
        $enableQuicksightAnalyticsRequestTransfer->setQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer);

        $enableQuicksightAnalyticsResponseTransfer = $this->quicksightAnalyticsRequestValidator->validateEnableQuicksightAnalyticsRequest(
            $enableQuicksightAnalyticsRequestTransfer,
            new EnableQuicksightAnalyticsResponseTransfer(),
        );

        if ($enableQuicksightAnalyticsResponseTransfer->getErrors()->count() !== 0) {
            return $enableQuicksightAnalyticsResponseTransfer;
        }

        $quicksightDeleteAssetBundleDataSetsResponseTransfer = $this->dataSetDeleter->deleteAssetBundleDataSets();

        if ($quicksightDeleteAssetBundleDataSetsResponseTransfer->getErrors()->count() !== 0) {
            return $enableQuicksightAnalyticsResponseTransfer->setErrors(
                $quicksightDeleteAssetBundleDataSetsResponseTransfer->getErrors(),
            );
        }

        $enableQuicksightAnalyticsRequestTransfer->setAssetBundleImportSourceBody(
            $this->assetBundleImportFileContentLoader->getAssetBundleImportFileContent(),
        );

        return $this->quicksightAssetBundleImportJobCreator
            ->createDefaultAssetBundleImportJobByEnableQuicksightAnalyticsRequest($enableQuicksightAnalyticsRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer
     */
    public function resetAnalytics(
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
    ): ResetQuicksightAnalyticsResponseTransfer {
        $resetQuicksightAnalyticsRequestTransfer->requireAssetBundleImportJobId();
        $resetQuicksightAnalyticsRequestTransfer->requireUser();

        $quicksightAssetBundleImportJobTransfer = $this->amazonQuicksightRepository
            ->findQuicksightAssetBundleImportJobByJobId($resetQuicksightAnalyticsRequestTransfer->getAssetBundleImportJobIdOrFail());
        $resetQuicksightAnalyticsRequestTransfer->setQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer);

        $resetQuicksightAnalyticsResponseTransfer = $this->quicksightAnalyticsRequestValidator
            ->validateResetQuicksightAnalyticsRequest(
                $resetQuicksightAnalyticsRequestTransfer,
                new ResetQuicksightAnalyticsResponseTransfer(),
            );

        if ($resetQuicksightAnalyticsResponseTransfer->getErrors()->count() !== 0) {
            return $resetQuicksightAnalyticsResponseTransfer;
        }

        $quicksightDeleteAssetBundleDataSetsResponseTransfer = $this->dataSetDeleter->deleteAssetBundleDataSets();

        if ($quicksightDeleteAssetBundleDataSetsResponseTransfer->getErrors()->count() !== 0) {
            return $resetQuicksightAnalyticsResponseTransfer->setErrors(
                $quicksightDeleteAssetBundleDataSetsResponseTransfer->getErrors(),
            );
        }

        $resetQuicksightAnalyticsRequestTransfer->setAssetBundleImportSourceBody(
            $this->assetBundleImportFileContentLoader->getAssetBundleImportFileContent(),
        );

        return $this->quicksightAssetBundleImportJobUpdater
            ->resetDefaultQuicksightAssetBundleImportJob($resetQuicksightAnalyticsRequestTransfer);
    }
}
