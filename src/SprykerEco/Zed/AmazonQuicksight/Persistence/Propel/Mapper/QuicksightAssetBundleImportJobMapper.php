<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob;
use Propel\Runtime\Collection\ObjectCollection;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Service\AmazonQuicksightToUtilEncodingServiceInterface;

class QuicksightAssetBundleImportJobMapper
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Dependency\Service\AmazonQuicksightToUtilEncodingServiceInterface
     */
    protected AmazonQuicksightToUtilEncodingServiceInterface $utilEncodingService;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Dependency\Service\AmazonQuicksightToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(AmazonQuicksightToUtilEncodingServiceInterface $utilEncodingService)
    {
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob> $quicksightAssetBundleImportJobEntities
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer $quicksightAssetBundleImportJobCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer
     */
    public function mapQuicksightAssetBundleImportJobEntitiesToQuicksightAssetBundleImportJobCollectionTransfer(
        ObjectCollection $quicksightAssetBundleImportJobEntities,
        QuicksightAssetBundleImportJobCollectionTransfer $quicksightAssetBundleImportJobCollectionTransfer
    ): QuicksightAssetBundleImportJobCollectionTransfer {
        foreach ($quicksightAssetBundleImportJobEntities as $quicksightAssetBundleImportJobEntity) {
            $quicksightAssetBundleImportJobCollectionTransfer->addQuicksightAssetBundleImportJob(
                $this->mapQuicksightAssetBundleImportJobEntityToQuicksightAssetBundleImportJobTransfer(
                    $quicksightAssetBundleImportJobEntity,
                    new QuicksightAssetBundleImportJobTransfer(),
                ),
            );
        }

        return $quicksightAssetBundleImportJobCollectionTransfer;
    }

    /**
     * @param \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob $quicksightAssetBundleImportJobEntity
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function mapQuicksightAssetBundleImportJobEntityToQuicksightAssetBundleImportJobTransfer(
        SpyQuicksightAssetBundleImportJob $quicksightAssetBundleImportJobEntity,
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        $quicksightAssetBundleImportJobData = $quicksightAssetBundleImportJobEntity->toArray();
        $quicksightAssetBundleImportJobData[QuicksightAssetBundleImportJobTransfer::ERRORS] = $this->utilEncodingService
            ->decodeJson($quicksightAssetBundleImportJobData[QuicksightAssetBundleImportJobTransfer::ERRORS], true);
        $quicksightAssetBundleImportJobTransfer->setErrors(new ArrayObject());
        if (isset($quicksightAssetBundleImportJobData[QuicksightAssetBundleImportJobTransfer::ERRORS])) {
            foreach ($quicksightAssetBundleImportJobData[QuicksightAssetBundleImportJobTransfer::ERRORS] as $error) {
                $quicksightAssetBundleImportJobTransfer->addError((new ErrorTransfer())->fromArray($error));
            }
            unset($quicksightAssetBundleImportJobData[QuicksightAssetBundleImportJobTransfer::ERRORS]);
        }

        return $quicksightAssetBundleImportJobTransfer->fromArray($quicksightAssetBundleImportJobData, true);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     * @param \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob $quicksightAssetBundleImportJobEntity
     *
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob
     */
    public function mapQuicksightAssetBundleImportJobTransferToQuicksightAssetBundleImportJobEntity(
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
        SpyQuicksightAssetBundleImportJob $quicksightAssetBundleImportJobEntity
    ): SpyQuicksightAssetBundleImportJob {
        $quicksightAssetBundleImportJobData = $quicksightAssetBundleImportJobTransfer->toArray();
        $quicksightAssetBundleImportJobData[QuicksightAssetBundleImportJobTransfer::ERRORS] = $this->utilEncodingService
            ->encodeJson(($quicksightAssetBundleImportJobData[QuicksightAssetBundleImportJobTransfer::ERRORS]));

        return $quicksightAssetBundleImportJobEntity->fromArray($quicksightAssetBundleImportJobData);
    }
}
