<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Enabler;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightAssetBundleImportJobCreatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Processor\AssetBundleQuicksightUserProcessorInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdaterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;

class AssetBundleEnabler implements AssetBundleEnablerInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface
     */
    protected AmazonQuicksightApiClientInterface $amazonQuicksightApiClient;

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
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Processor\AssetBundleQuicksightUserProcessorInterface
     */
    protected AssetBundleQuicksightUserProcessorInterface $assetBundleQuicksightUserProcessor;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface
     */
    protected AssetBundleImportFileContentLoaderInterface $assetBundleImportFileContentLoader;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface $amazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightAssetBundleImportJobCreatorInterface $quicksightAssetBundleImportJobCreator
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdaterInterface $quicksightAssetBundleImportJobUpdater
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface $amazonQuicksightRepository
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Processor\AssetBundleQuicksightUserProcessorInterface $assetBundleQuicksightUserProcessor
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface $assetBundleImportFileContentLoader
     */
    public function __construct(
        AmazonQuicksightApiClientInterface $amazonQuicksightApiClient,
        QuicksightAssetBundleImportJobCreatorInterface $quicksightAssetBundleImportJobCreator,
        QuicksightAssetBundleImportJobUpdaterInterface $quicksightAssetBundleImportJobUpdater,
        AmazonQuicksightRepositoryInterface $amazonQuicksightRepository,
        QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator,
        AssetBundleQuicksightUserProcessorInterface $assetBundleQuicksightUserProcessor,
        AssetBundleImportFileContentLoaderInterface $assetBundleImportFileContentLoader
    ) {
        $this->amazonQuicksightApiClient = $amazonQuicksightApiClient;
        $this->quicksightAssetBundleImportJobCreator = $quicksightAssetBundleImportJobCreator;
        $this->quicksightAssetBundleImportJobUpdater = $quicksightAssetBundleImportJobUpdater;
        $this->amazonQuicksightRepository = $amazonQuicksightRepository;
        $this->quicksightAnalyticsRequestValidator = $quicksightAnalyticsRequestValidator;
        $this->assetBundleQuicksightUserProcessor = $assetBundleQuicksightUserProcessor;
        $this->assetBundleImportFileContentLoader = $assetBundleImportFileContentLoader;
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

        $userCollectionResponseTransfer = $this->assetBundleQuicksightUserProcessor->processQuicksightUserBeforeAnalyticsEnabling(
            $enableQuicksightAnalyticsRequestTransfer->getUserOrFail(),
        );

        if ($userCollectionResponseTransfer->getErrors()->count() !== 0) {
            return $enableQuicksightAnalyticsResponseTransfer->setErrors($userCollectionResponseTransfer->getErrors());
        }

        $enableQuicksightAnalyticsRequestTransfer->setAssetBundleImportSourceBody(
            $this->assetBundleImportFileContentLoader->getAssetBundleImportFileContent(),
        );

        $quicksightStartAssetBundleImportJobResponseTransfer = $this->amazonQuicksightApiClient
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

        $resetQuicksightAnalyticsRequestTransfer->setAssetBundleImportSourceBody(
            $this->assetBundleImportFileContentLoader->getAssetBundleImportFileContent(),
        );

        $quicksightStartAssetBundleImportJobResponseTransfer = $this->amazonQuicksightApiClient
            ->startAssetBundleImportJobByResetQuicksightAnalyticsRequest($resetQuicksightAnalyticsRequestTransfer);

        if ($quicksightStartAssetBundleImportJobResponseTransfer->getErrors()->count() !== 0) {
            return $resetQuicksightAnalyticsResponseTransfer->setErrors(
                $quicksightStartAssetBundleImportJobResponseTransfer->getErrors(),
            );
        }

        $quicksightAssetBundleImportJobTransfer = $this->quicksightAssetBundleImportJobUpdater
            ->resetDefaultQuicksightAssetBundleImportJob(
                $resetQuicksightAnalyticsRequestTransfer->getQuicksightAssetBundleImportJobOrFail(),
            );

        return $resetQuicksightAnalyticsResponseTransfer
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
                ->resetDefaultQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer);
        }

        return $this->quicksightAssetBundleImportJobCreator
            ->createDefaultAssetBundleImportJobByEnableQuicksightAnalyticsRequest($enableQuicksightAnalyticsRequestTransfer);
    }
}
