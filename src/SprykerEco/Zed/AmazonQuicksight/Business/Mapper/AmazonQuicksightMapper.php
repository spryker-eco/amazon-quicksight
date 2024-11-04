<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Mapper;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportSourceTransfer;
use Generated\Shared\Transfer\QuicksightDeleteAssetBundleDataSetsResponseTransfer;
use Generated\Shared\Transfer\QuicksightDeleteDataSetResponseTransfer;
use Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer;
use Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer;
use Generated\Shared\Transfer\QuicksightEmbedUrlTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\UserTransfer;

class AmazonQuicksightMapper implements AmazonQuicksightMapperInterface
{
    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_GenerateEmbedUrlForRegisteredUser.html#API_GenerateEmbedUrlForRegisteredUser_ResponseSyntax
     *
     * @var string
     */
    protected const RESPONSE_KEY_EMBED_URL = 'EmbedUrl';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DescribeAssetBundleImportJob.html#API_DescribeAssetBundleImportJob_ResponseSyntax
     *
     * @var string
     */
    protected const RESPONSE_KEY_ERRORS = 'Errors';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DescribeAssetBundleImportJob.html#API_DescribeAssetBundleImportJob_ResponseSyntax
     *
     * @var string
     */
    protected const RESPONSE_KEY_ERRORS_MESSAGE = 'Message';

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer $quicksightUserRegisterRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer
     */
    public function mapUserTransferToQuicksightUserRegisterRequestTransfer(
        UserTransfer $userTransfer,
        QuicksightUserRegisterRequestTransfer $quicksightUserRegisterRequestTransfer
    ): QuicksightUserRegisterRequestTransfer {
        return $quicksightUserRegisterRequestTransfer
            ->setEmail($userTransfer->getUsernameOrFail())
            ->setUserName($userTransfer->getUsernameOrFail())
            ->setUserRole(strtoupper($userTransfer->getQuicksightUserOrFail()->getRoleOrFail()));
    }

    /**
     * @param array<string, mixed> $quicksightUserData
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function mapQuicksightUserDataToQuicksightUserTransfer(
        array $quicksightUserData,
        QuicksightUserTransfer $quicksightUserTransfer
    ): QuicksightUserTransfer {
        return $quicksightUserTransfer->fromArray($quicksightUserData, true);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer $quicksightGenerateEmbedUrlRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer
     */
    public function mapQuicksightUserTransferToQuicksightGenerateEmbedUrlRequestTransfer(
        QuicksightUserTransfer $quicksightUserTransfer,
        QuicksightGenerateEmbedUrlRequestTransfer $quicksightGenerateEmbedUrlRequestTransfer
    ): QuicksightGenerateEmbedUrlRequestTransfer {
        return $quicksightGenerateEmbedUrlRequestTransfer->setUserArn($quicksightUserTransfer->getArnOrFail());
    }

    /**
     * @param array<string, mixed> $generateEmbedUrlResponseData
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer
     */
    public function mapGenerateEmbedUrlResponseDataToQuicksightGenerateEmbedUrlResponseTransfer(
        array $generateEmbedUrlResponseData,
        QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
    ): QuicksightGenerateEmbedUrlResponseTransfer {
        return $quicksightGenerateEmbedUrlResponseTransfer->setEmbedUrl(
            (new QuicksightEmbedUrlTransfer())->setUrl($generateEmbedUrlResponseData[static::RESPONSE_KEY_EMBED_URL]),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param \Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer $quicksightDeleteUserRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer
     */
    public function mapUserTransferToQuicksightDeleteUserRequestTransfer(
        UserTransfer $userTransfer,
        QuicksightDeleteUserRequestTransfer $quicksightDeleteUserRequestTransfer
    ): QuicksightDeleteUserRequestTransfer {
        return $quicksightDeleteUserRequestTransfer->setUserName($userTransfer->getUsernameOrFail());
    }

    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     * @param \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer $quicksightStartAssetBundleImportJobRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer
     */
    public function mapEnableQuicksightAnalyticsRequestTransferToQuicksightStartAssetBundleImportJobRequestTransfer(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer,
        QuicksightStartAssetBundleImportJobRequestTransfer $quicksightStartAssetBundleImportJobRequestTransfer
    ): QuicksightStartAssetBundleImportJobRequestTransfer {
        $quicksightStartAssetBundleImportJobRequestTransfer->fromArray(
            $enableQuicksightAnalyticsRequestTransfer->toArray(),
            true,
        );
        $quicksightStartAssetBundleImportJobRequestTransfer->setAssetBundleImportSource(
            (new QuicksightAssetBundleImportSourceTransfer())
                ->setBody($enableQuicksightAnalyticsRequestTransfer->getAssetBundleImportSourceBody()),
        );
        $userArn = $enableQuicksightAnalyticsRequestTransfer->getUserOrFail()->getQuicksightUserOrFail()->getArnOrFail();
        $quicksightStartAssetBundleImportJobRequestTransfer->getOverridePermissionsOrFail()
            ->getDashboards()
            ->getIterator()
            ->current()
            ->getPermissionsOrFail()
            ->addPrincipal($userArn);
        $quicksightStartAssetBundleImportJobRequestTransfer->getOverridePermissionsOrFail()
            ->getAnalyses()
            ->getIterator()
            ->current()
            ->getPermissionsOrFail()
            ->addPrincipal($userArn);
        $quicksightStartAssetBundleImportJobRequestTransfer->getOverridePermissionsOrFail()
            ->getDataSets()
            ->getIterator()
            ->current()
            ->getPermissionsOrFail()
            ->addPrincipal($userArn);
        $quicksightStartAssetBundleImportJobRequestTransfer->getOverridePermissionsOrFail()
            ->getDataSources()
            ->getIterator()
            ->current()
            ->getPermissionsOrFail()
            ->addPrincipal($userArn);

        return $quicksightStartAssetBundleImportJobRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     * @param \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer $quicksightStartAssetBundleImportJobRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer
     */
    public function mapResetQuicksightAnalyticsRequestTransferToQuicksightStartAssetBundleImportJobRequestTransfer(
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer,
        QuicksightStartAssetBundleImportJobRequestTransfer $quicksightStartAssetBundleImportJobRequestTransfer
    ): QuicksightStartAssetBundleImportJobRequestTransfer {
        $quicksightStartAssetBundleImportJobRequestTransfer->fromArray(
            $resetQuicksightAnalyticsRequestTransfer->toArray(),
            true,
        );
        $quicksightStartAssetBundleImportJobRequestTransfer->setAssetBundleImportSource(
            (new QuicksightAssetBundleImportSourceTransfer())
                ->setBody($resetQuicksightAnalyticsRequestTransfer->getAssetBundleImportSourceBody()),
        );
        $userArn = $resetQuicksightAnalyticsRequestTransfer->getUserOrFail()->getQuicksightUserOrFail()->getArnOrFail();
        $quicksightStartAssetBundleImportJobRequestTransfer->getOverridePermissionsOrFail()
            ->getDashboards()
            ->getIterator()
            ->current()
            ->getPermissionsOrFail()
            ->addPrincipal($userArn);
        $quicksightStartAssetBundleImportJobRequestTransfer->getOverridePermissionsOrFail()
            ->getAnalyses()
            ->getIterator()
            ->current()
            ->getPermissionsOrFail()
            ->addPrincipal($userArn);
        $quicksightStartAssetBundleImportJobRequestTransfer->getOverridePermissionsOrFail()
            ->getDataSets()
            ->getIterator()
            ->current()
            ->getPermissionsOrFail()
            ->addPrincipal($userArn);
        $quicksightStartAssetBundleImportJobRequestTransfer->getOverridePermissionsOrFail()
            ->getDataSources()
            ->getIterator()
            ->current()
            ->getPermissionsOrFail()
            ->addPrincipal($userArn);

        return $quicksightStartAssetBundleImportJobRequestTransfer;
    }

    /**
     * @param array<string, mixed> $describeAssetBundleImportJobData
     * @param \Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer $quicksightDescribeAssetBundleImportJobResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer
     */
    public function mapDescribeAssetBundleImportJobDataToQuicksightDescribeAssetBundleImportJobResponseTransfer(
        array $describeAssetBundleImportJobData,
        QuicksightDescribeAssetBundleImportJobResponseTransfer $quicksightDescribeAssetBundleImportJobResponseTransfer
    ): QuicksightDescribeAssetBundleImportJobResponseTransfer {
        if (isset($describeAssetBundleImportJobData[static::RESPONSE_KEY_ERRORS])) {
            foreach ($describeAssetBundleImportJobData[static::RESPONSE_KEY_ERRORS] as $error) {
                if (!isset($error[static::RESPONSE_KEY_ERRORS_MESSAGE])) {
                    continue;
                }
                $quicksightDescribeAssetBundleImportJobResponseTransfer->addError(
                    (new ErrorTransfer())->setMessage($error[static::RESPONSE_KEY_ERRORS_MESSAGE]),
                );
            }

            unset($describeAssetBundleImportJobData[static::RESPONSE_KEY_ERRORS]);
        }

        return $quicksightDescribeAssetBundleImportJobResponseTransfer->fromArray($describeAssetBundleImportJobData, true);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer $quicksightDescribeAssetBundleImportJobResponseTransfer
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function mapQuicksightDescribeAssetBundleImportJobResponseTransferToQuicksightAssetBundleImportJobTransfer(
        QuicksightDescribeAssetBundleImportJobResponseTransfer $quicksightDescribeAssetBundleImportJobResponseTransfer,
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        $quicksightAssetBundleImportJobTransfer->setStatus(
            $quicksightDescribeAssetBundleImportJobResponseTransfer->getJobStatusOrFail(),
        );

        return $quicksightAssetBundleImportJobTransfer->fromArray($quicksightDescribeAssetBundleImportJobResponseTransfer->toArray(), true);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightDeleteDataSetResponseTransfer $quicksightDeleteDataSetResponseTransfer
     * @param \Generated\Shared\Transfer\QuicksightDeleteAssetBundleDataSetsResponseTransfer $quicksightDeleteAssetBundleDataSetsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteAssetBundleDataSetsResponseTransfer
     */
    public function mapQuicksightDeleteDataSetResponseTransferToQuicksightDeleteAssetBundleDataSetsResponseTransfer(
        QuicksightDeleteDataSetResponseTransfer $quicksightDeleteDataSetResponseTransfer,
        QuicksightDeleteAssetBundleDataSetsResponseTransfer $quicksightDeleteAssetBundleDataSetsResponseTransfer
    ): QuicksightDeleteAssetBundleDataSetsResponseTransfer {
        foreach ($quicksightDeleteDataSetResponseTransfer->getErrors() as $errorTransfer) {
            $quicksightDeleteAssetBundleDataSetsResponseTransfer->addError($errorTransfer);
        }

        return $quicksightDeleteAssetBundleDataSetsResponseTransfer;
    }
}
