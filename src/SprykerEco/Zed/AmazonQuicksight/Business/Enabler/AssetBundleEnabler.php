<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Enabler;

use ArrayObject;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Processor\AssetBundleQuicksightUserProcessorInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;

class AssetBundleEnabler implements AssetBundleEnablerInterface
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
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface $amazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface $amazonQuicksightRepository
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Processor\AssetBundleQuicksightUserProcessorInterface $assetBundleQuicksightUserProcessor
     */
    public function __construct(
        AmazonQuicksightApiClientInterface $amazonQuicksightApiClient,
        AmazonQuicksightConfig $amazonQuicksightConfig,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager,
        AmazonQuicksightRepositoryInterface $amazonQuicksightRepository,
        QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator,
        AssetBundleQuicksightUserProcessorInterface $assetBundleQuicksightUserProcessor
    ) {
        $this->amazonQuicksightApiClient = $amazonQuicksightApiClient;
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->amazonQuicksightEntityManager = $amazonQuicksightEntityManager;
        $this->amazonQuicksightRepository = $amazonQuicksightRepository;
        $this->quicksightAnalyticsRequestValidator = $quicksightAnalyticsRequestValidator;
        $this->assetBundleQuicksightUserProcessor = $assetBundleQuicksightUserProcessor;
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

        $enableQuicksightAnalyticsRequestTransfer->setAssetBundleImportSourceBody($this->getAssetBundleImportFileContent());

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

        $resetQuicksightAnalyticsRequestTransfer->setAssetBundleImportSourceBody($this->getAssetBundleImportFileContent());

        $quicksightStartAssetBundleImportJobResponseTransfer = $this->amazonQuicksightApiClient
            ->startAssetBundleImportJobByResetQuicksightAnalyticsRequest($resetQuicksightAnalyticsRequestTransfer);

        if ($quicksightStartAssetBundleImportJobResponseTransfer->getErrors()->count() !== 0) {
            return $resetQuicksightAnalyticsResponseTransfer->setErrors(
                $quicksightStartAssetBundleImportJobResponseTransfer->getErrors(),
            );
        }

        $quicksightAssetBundleImportJobTransfer = $this->resetDefaultQuicksightAssetBundleImportJob(
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
            return $this->resetDefaultQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer);
        }

        return $this->amazonQuicksightEntityManager->createQuicksightAssetBundleImportJob(
            (new QuicksightAssetBundleImportJobTransfer())
                ->setStatus($this->amazonQuicksightConfig->getDefaultNewAssetBundleImportJobStatus())
                ->setJobId($enableQuicksightAnalyticsRequestTransfer->getAssetBundleImportJobId()),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    protected function resetDefaultQuicksightAssetBundleImportJob(
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        $quicksightAssetBundleImportJobTransfer->setStatus($this->amazonQuicksightConfig->getDefaultNewAssetBundleImportJobStatus());
        $quicksightAssetBundleImportJobTransfer->setErrors(new ArrayObject());
        $quicksightAssetBundleImportJobTransfer = $this->amazonQuicksightEntityManager
            ->updateQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer);

        return $quicksightAssetBundleImportJobTransfer;
    }

    /**
     * @return string
     */
    protected function getAssetBundleImportFileContent(): string
    {
        return file_get_contents($this->amazonQuicksightConfig->getAssetBundleImportFilePath());
    }
}
