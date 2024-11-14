<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Expander;

use Generated\Shared\Transfer\AnalyticsActionTransfer;
use Generated\Shared\Transfer\AnalyticsCollectionTransfer;
use Generated\Shared\Transfer\AnalyticsRequestTransfer;
use Generated\Shared\Transfer\AnalyticsTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Synchronizer\QuicksightAssetBundleImportJobSynchronizerInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;
use Twig\Environment;

class AnalyticsExpander implements AnalyticsExpanderInterface
{
    /**
     * @var string
     */
    protected const TEMPLATE_PATH_QUICKSIGHT_ANALYTICS = '@AmazonQuicksight/_partials/quicksight-analytics.twig';

    /**
     * @var string
     */
    protected const TEMPLATE_PATH_QUICKSIGHT_ANALYTICS_RESET_ACTION = '@AmazonQuicksight/_partials/quicksight-analytics-reset-action.twig';

    /**
     * @var string
     */
    protected const TEMPLATE_PATH_SYNCHRONIZE_QUICKSIGHT_USERS_ACTION = '@AmazonQuicksight/_partials/synchronize-quicksight-users-action.twig';

    /**
     * @var string
     */
    protected const FORM_NAME_SYNCHRONIZE_QUICKSIGHT_USERS = 'synchronizeQuicksightUsersForm';

    /**
     * @var string
     */
    protected const FIELD_NAME_SYNCHRONIZE_QUICKSIGHT_USERS_FORM_TOKEN = '_token';

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface
     */
    protected AmazonQuicksightRepositoryInterface $amazonQuicksightRepository;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface
     */
    protected UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface
     */
    protected QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Synchronizer\QuicksightAssetBundleImportJobSynchronizerInterface
     */
    protected QuicksightAssetBundleImportJobSynchronizerInterface $quicksightAssetBundleImportJobSynchronizer;

    /**
     * @var \Twig\Environment
     */
    protected Environment $twigEnvironment;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface $amazonQuicksightRepository
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Synchronizer\QuicksightAssetBundleImportJobSynchronizerInterface $quicksightAssetBundleImportJobSynchronizer
     * @param \Twig\Environment $twigEnvironment
     */
    public function __construct(
        AmazonQuicksightRepositoryInterface $amazonQuicksightRepository,
        AmazonQuicksightConfig $amazonQuicksightConfig,
        UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient,
        QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator,
        QuicksightAssetBundleImportJobSynchronizerInterface $quicksightAssetBundleImportJobSynchronizer,
        Environment $twigEnvironment
    ) {
        $this->amazonQuicksightRepository = $amazonQuicksightRepository;
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->userAmazonQuicksightApiClient = $userAmazonQuicksightApiClient;
        $this->quicksightAnalyticsRequestValidator = $quicksightAnalyticsRequestValidator;
        $this->quicksightAssetBundleImportJobSynchronizer = $quicksightAssetBundleImportJobSynchronizer;
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @param \Generated\Shared\Transfer\AnalyticsRequestTransfer $analyticsRequestTransfer
     * @param \Generated\Shared\Transfer\AnalyticsCollectionTransfer $analyticsCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsCollectionTransfer
     */
    public function expandAnalyticsCollectionWithQuicksightAnalytics(
        AnalyticsRequestTransfer $analyticsRequestTransfer,
        AnalyticsCollectionTransfer $analyticsCollectionTransfer
    ): AnalyticsCollectionTransfer {
        $userTransfer = $analyticsRequestTransfer->getUserOrFail();
        $quicksightUserTransfer = $this->findQuicksightUser($userTransfer);

        $quicksightAssetBundleImportJobTransfer = $this->quicksightAssetBundleImportJobSynchronizer
            ->findSyncedDefaultQuicksightAssetBundleImportJob();

        $analyticsCollectionTransfer = $this->expandAnalytics(
            $analyticsCollectionTransfer,
            $quicksightAssetBundleImportJobTransfer,
            $quicksightUserTransfer,
        );

        return $this->expandAnalyticsActions(
            $analyticsCollectionTransfer,
            $quicksightAssetBundleImportJobTransfer,
            $quicksightUserTransfer,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AnalyticsCollectionTransfer $analyticsCollectionTransfer
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer|null $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsCollectionTransfer
     */
    protected function expandAnalytics(
        AnalyticsCollectionTransfer $analyticsCollectionTransfer,
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
        ?QuicksightUserTransfer $quicksightUserTransfer
    ): AnalyticsCollectionTransfer {
        $content = $this->twigEnvironment->render(
            static::TEMPLATE_PATH_QUICKSIGHT_ANALYTICS,
            [
                'quicksightGenerateEmbedUrlResponse' => $this->getQuicksightGenerateEmbedUrlResponseTransfer(
                    $this->quicksightAnalyticsRequestValidator
                        ->isAssetBundleSuccessfullyInitialized($quicksightAssetBundleImportJobTransfer),
                    $this->quicksightAnalyticsRequestValidator->isQuicksightUserRoleAvailable($quicksightUserTransfer),
                    $quicksightUserTransfer,
                ),
                'quicksightAssetBundleImportJob' => $quicksightAssetBundleImportJobTransfer,
                'isAssetBundleInitializationInProgress' => $this->quicksightAnalyticsRequestValidator
                    ->isAssetBundleInitializationInProgress($quicksightAssetBundleImportJobTransfer),
                'isEnableAnalyticsEnabled' => $this->quicksightAnalyticsRequestValidator->isEnableAnalyticsEnabled(
                    $quicksightAssetBundleImportJobTransfer,
                    $quicksightUserTransfer,
                ),
                'isDisplayAnalyticsEnabled' => $this->quicksightAnalyticsRequestValidator->isDisplayAnalyticsEnabled(
                    $quicksightAssetBundleImportJobTransfer,
                    $quicksightUserTransfer,
                ),
            ],
        );

        $analyticsCollectionTransfer->addAnalytics((new AnalyticsTransfer())->setContent($content));

        return $analyticsCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AnalyticsCollectionTransfer $analyticsCollectionTransfer
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer|null $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsCollectionTransfer
     */
    protected function expandAnalyticsActions(
        AnalyticsCollectionTransfer $analyticsCollectionTransfer,
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
        ?QuicksightUserTransfer $quicksightUserTransfer
    ): AnalyticsCollectionTransfer {
        $analyticsCollectionTransfer->addAnalyticsAction((new AnalyticsActionTransfer())->setContent(
            $this->twigEnvironment->render(static::TEMPLATE_PATH_SYNCHRONIZE_QUICKSIGHT_USERS_ACTION, [
                'formName' => static::FORM_NAME_SYNCHRONIZE_QUICKSIGHT_USERS,
                'tokenFieldName' => static::FIELD_NAME_SYNCHRONIZE_QUICKSIGHT_USERS_FORM_TOKEN,
            ]),
        ));

        if (!$this->quicksightAnalyticsRequestValidator->isResetAnalyticsEnabled($quicksightAssetBundleImportJobTransfer, $quicksightUserTransfer)) {
            return $analyticsCollectionTransfer;
        }

        $analyticsCollectionTransfer->addAnalyticsAction((new AnalyticsActionTransfer())->setContent(
            $this->twigEnvironment->render(static::TEMPLATE_PATH_QUICKSIGHT_ANALYTICS_RESET_ACTION),
        ));

        return $analyticsCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer|null
     */
    protected function findQuicksightUser(UserTransfer $userTransfer): ?QuicksightUserTransfer
    {
        $quicksightUserTransfers = $this->amazonQuicksightRepository
            ->getQuicksightUsersByUserIds([$userTransfer->getIdUserOrFail()]);

        if (!isset($quicksightUserTransfers[0])) {
            return null;
        }

        return $quicksightUserTransfers[0];
    }

    /**
     * @param bool $isAssetBundleSuccessfullyInitialized
     * @param bool $isQuicksightUserRoleAvailable
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer|null $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer
     */
    protected function getQuicksightGenerateEmbedUrlResponseTransfer(
        bool $isAssetBundleSuccessfullyInitialized,
        bool $isQuicksightUserRoleAvailable,
        ?QuicksightUserTransfer $quicksightUserTransfer
    ): QuicksightGenerateEmbedUrlResponseTransfer {
        if (!$isAssetBundleSuccessfullyInitialized || !$isQuicksightUserRoleAvailable || !$quicksightUserTransfer) {
            return new QuicksightGenerateEmbedUrlResponseTransfer();
        }

        return $this->userAmazonQuicksightApiClient->generateEmbedUrlForRegisteredUser($quicksightUserTransfer);
    }
}
