<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Mapper;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightDeleteAssetBundleDataSetsResponseTransfer;
use Generated\Shared\Transfer\QuicksightDeleteDataSetResponseTransfer;
use Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer;
use Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\UserTransfer;

interface AmazonQuicksightMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer $quicksightUserRegisterRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer
     */
    public function mapUserTransferToQuicksightUserRegisterRequestTransfer(
        UserTransfer $userTransfer,
        QuicksightUserRegisterRequestTransfer $quicksightUserRegisterRequestTransfer
    ): QuicksightUserRegisterRequestTransfer;

    /**
     * @param array<string, mixed> $quicksightUserData
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function mapQuicksightUserDataToQuicksightUserTransfer(
        array $quicksightUserData,
        QuicksightUserTransfer $quicksightUserTransfer
    ): QuicksightUserTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer $quicksightGenerateEmbedUrlRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer
     */
    public function mapQuicksightUserTransferToQuicksightGenerateEmbedUrlRequestTransfer(
        QuicksightUserTransfer $quicksightUserTransfer,
        QuicksightGenerateEmbedUrlRequestTransfer $quicksightGenerateEmbedUrlRequestTransfer
    ): QuicksightGenerateEmbedUrlRequestTransfer;

    /**
     * @param array<string, mixed> $generateEmbedUrlResponseData
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer
     */
    public function mapGenerateEmbedUrlResponseDataToQuicksightGenerateEmbedUrlResponseTransfer(
        array $generateEmbedUrlResponseData,
        QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
    ): QuicksightGenerateEmbedUrlResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     * @param \Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer $quicksightDeleteUserRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer
     */
    public function mapQuicksightUserTransferToQuicksightDeleteUserRequestTransfer(
        QuicksightUserTransfer $quicksightUserTransfer,
        QuicksightDeleteUserRequestTransfer $quicksightDeleteUserRequestTransfer
    ): QuicksightDeleteUserRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param \Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer $quicksightDeleteUserRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer
     */
    public function mapUserTransferToQuicksightDeleteUserRequestTransfer(
        UserTransfer $userTransfer,
        QuicksightDeleteUserRequestTransfer $quicksightDeleteUserRequestTransfer
    ): QuicksightDeleteUserRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     * @param \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer $quicksightStartAssetBundleImportJobRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer
     */
    public function mapEnableQuicksightAnalyticsRequestTransferToQuicksightStartAssetBundleImportJobRequestTransfer(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer,
        QuicksightStartAssetBundleImportJobRequestTransfer $quicksightStartAssetBundleImportJobRequestTransfer
    ): QuicksightStartAssetBundleImportJobRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     * @param \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer $quicksightStartAssetBundleImportJobRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer
     */
    public function mapResetQuicksightAnalyticsRequestTransferToQuicksightStartAssetBundleImportJobRequestTransfer(
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer,
        QuicksightStartAssetBundleImportJobRequestTransfer $quicksightStartAssetBundleImportJobRequestTransfer
    ): QuicksightStartAssetBundleImportJobRequestTransfer;

    /**
     * @param array<string, mixed> $describeAssetBundleImportJobData
     * @param \Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer $quicksightDescribeAssetBundleImportJobResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer
     */
    public function mapDescribeAssetBundleImportJobDataToQuicksightDescribeAssetBundleImportJobResponseTransfer(
        array $describeAssetBundleImportJobData,
        QuicksightDescribeAssetBundleImportJobResponseTransfer $quicksightDescribeAssetBundleImportJobResponseTransfer
    ): QuicksightDescribeAssetBundleImportJobResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer $quicksightDescribeAssetBundleImportJobResponseTransfer
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function mapQuicksightDescribeAssetBundleImportJobResponseTransferToQuicksightAssetBundleImportJobTransfer(
        QuicksightDescribeAssetBundleImportJobResponseTransfer $quicksightDescribeAssetBundleImportJobResponseTransfer,
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): QuicksightAssetBundleImportJobTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuicksightDeleteDataSetResponseTransfer $quicksightDeleteDataSetResponseTransfer
     * @param \Generated\Shared\Transfer\QuicksightDeleteAssetBundleDataSetsResponseTransfer $quicksightDeleteAssetBundleDataSetsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteAssetBundleDataSetsResponseTransfer
     */
    public function mapQuicksightDeleteDataSetResponseTransferToQuicksightDeleteAssetBundleDataSetsResponseTransfer(
        QuicksightDeleteDataSetResponseTransfer $quicksightDeleteDataSetResponseTransfer,
        QuicksightDeleteAssetBundleDataSetsResponseTransfer $quicksightDeleteAssetBundleDataSetsResponseTransfer
    ): QuicksightDeleteAssetBundleDataSetsResponseTransfer;
}
