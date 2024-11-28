<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Validator;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;

class QuicksightAnalyticsRequestValidator implements QuicksightAnalyticsRequestValidatorInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_ENABLE_ANALYTICS_FAILED = 'Failed to enable the analytics';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_RESET_ANALYTICS_FAILED = 'Failed to reset the analytics';

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     */
    public function __construct(AmazonQuicksightConfig $amazonQuicksightConfig)
    {
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer $enableQuicksightAnalyticsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer
     */
    public function validateEnableQuicksightAnalyticsRequest(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer,
        EnableQuicksightAnalyticsResponseTransfer $enableQuicksightAnalyticsResponseTransfer
    ): EnableQuicksightAnalyticsResponseTransfer {
        $quicksightAssetBundleImportJobTransfer = $enableQuicksightAnalyticsRequestTransfer->getQuicksightAssetBundleImportJob();
        $quicksightUserTransfer = $enableQuicksightAnalyticsRequestTransfer->getUserOrFail()->getQuicksightUser();

        if (!$this->isEnableAnalyticsAllowed($quicksightAssetBundleImportJobTransfer, $quicksightUserTransfer)) {
            $enableQuicksightAnalyticsResponseTransfer->addError(
                (new ErrorTransfer())->setMessage(static::ERROR_MESSAGE_ENABLE_ANALYTICS_FAILED),
            );
        }

        return $enableQuicksightAnalyticsResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer $resetQuicksightAnalyticsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer
     */
    public function validateResetQuicksightAnalyticsRequest(
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer,
        ResetQuicksightAnalyticsResponseTransfer $resetQuicksightAnalyticsResponseTransfer
    ): ResetQuicksightAnalyticsResponseTransfer {
        $quicksightAssetBundleImportJobTransfer = $resetQuicksightAnalyticsRequestTransfer->getQuicksightAssetBundleImportJob();
        $quicksightUserTransfer = $resetQuicksightAnalyticsRequestTransfer->getUserOrFail()->getQuicksightUser();

        if (!$this->isResetAnalyticsAllowed($quicksightAssetBundleImportJobTransfer, $quicksightUserTransfer)) {
            $resetQuicksightAnalyticsResponseTransfer->addError(
                (new ErrorTransfer())->setMessage(static::ERROR_MESSAGE_RESET_ANALYTICS_FAILED),
            );
        }

        return $resetQuicksightAnalyticsResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer|null $quicksightUserTransfer
     *
     * @return bool
     */
    public function isResetAnalyticsAllowed(
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
        ?QuicksightUserTransfer $quicksightUserTransfer
    ): bool {
        return $this->isAssetBundleSuccessfullyInitialized($quicksightAssetBundleImportJobTransfer)
            && !$this->isAssetBundleInitializationInProgress($quicksightAssetBundleImportJobTransfer)
            && $this->isQuicksightUserAuthor($quicksightUserTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer|null $quicksightUserTransfer
     *
     * @return bool
     */
    public function isEnableAnalyticsAllowed(
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
        ?QuicksightUserTransfer $quicksightUserTransfer
    ): bool {
        return !$this->isAssetBundleSuccessfullyInitialized($quicksightAssetBundleImportJobTransfer)
            && !$this->isAssetBundleInitializationInProgress($quicksightAssetBundleImportJobTransfer)
            && $this->isQuicksightUserAuthor($quicksightUserTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer|null $quicksightUserTransfer
     *
     * @return bool
     */
    public function isDisplayAnalyticsAllowed(
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
        ?QuicksightUserTransfer $quicksightUserTransfer
    ): bool {
        return $this->isAssetBundleSuccessfullyInitialized($quicksightAssetBundleImportJobTransfer)
            && !$this->isAssetBundleInitializationInProgress($quicksightAssetBundleImportJobTransfer)
            && $this->isQuicksightUserRoleAvailable($quicksightUserTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     *
     * @return bool
     */
    public function isAssetBundleSuccessfullyInitialized(
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): bool {
        return $quicksightAssetBundleImportJobTransfer
            && $quicksightAssetBundleImportJobTransfer->getIsInitialized();
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     *
     * @return bool
     */
    public function isAssetBundleInitializationInProgress(
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): bool {
        return $quicksightAssetBundleImportJobTransfer
            && !in_array($quicksightAssetBundleImportJobTransfer->getStatus(), $this->amazonQuicksightConfig->getAssetBundleImportJobCompletionStatuses());
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer|null $quicksightUserTransfer
     *
     * @return bool
     */
    public function isQuicksightUserRoleAvailable(?QuicksightUserTransfer $quicksightUserTransfer): bool
    {
        return $quicksightUserTransfer && in_array(
            $quicksightUserTransfer->getRole(),
            $this->amazonQuicksightConfig->getQuicksightUserRoles(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer|null $quicksightUserTransfer
     *
     * @return bool
     */
    protected function isQuicksightUserAuthor(?QuicksightUserTransfer $quicksightUserTransfer): bool
    {
        return $quicksightUserTransfer && in_array($quicksightUserTransfer->getRole(), $this->amazonQuicksightConfig->getQuicksightUserAuthorRoles());
    }
}
