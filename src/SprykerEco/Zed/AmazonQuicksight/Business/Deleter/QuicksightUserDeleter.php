<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Deleter;

use ArrayObject;
use Generated\Shared\Transfer\QuicksightUserCollectionDeleteCriteriaTransfer;
use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserCollectionTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReaderInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightUserDeleter implements QuicksightUserDeleterInterface
{
    use TransactionTrait;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReaderInterface
     */
    protected QuicksightUserReaderInterface $quicksightUserReader;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface
     */
    protected AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface
     */
    protected AmazonQuicksightApiClientInterface $amazonQuicksightApiClient;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReaderInterface $quicksightUserReader
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface $amazonQuicksightApiClient
     */
    public function __construct(
        QuicksightUserReaderInterface $quicksightUserReader,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager,
        AmazonQuicksightApiClientInterface $amazonQuicksightApiClient
    ) {
        $this->quicksightUserReader = $quicksightUserReader;
        $this->amazonQuicksightEntityManager = $amazonQuicksightEntityManager;
        $this->amazonQuicksightApiClient = $amazonQuicksightApiClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionDeleteCriteriaTransfer $quicksightUserCollectionDeleteCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function deleteQuicksightUserCollection(
        QuicksightUserCollectionDeleteCriteriaTransfer $quicksightUserCollectionDeleteCriteriaTransfer
    ): QuicksightUserCollectionResponseTransfer {
        $quicksightUserCollectionResponseTransfer = new QuicksightUserCollectionResponseTransfer();

        if ($quicksightUserCollectionDeleteCriteriaTransfer->getQuicksightUserIds() === []) {
            return $quicksightUserCollectionResponseTransfer;
        }

        $quicksightUserCollectionTransfer = $this->quicksightUserReader->getQuicksightUserCollectionByQuicksightUserIds(
            $quicksightUserCollectionDeleteCriteriaTransfer->getQuicksightUserIds(),
        );
        if ($quicksightUserCollectionTransfer->getQuicksightUsers()->count() === 0) {
            return $quicksightUserCollectionResponseTransfer;
        }

        return $this->getTransactionHandler()->handleTransaction(function () use ($quicksightUserCollectionTransfer, $quicksightUserCollectionResponseTransfer) {
            return $this->executeDeleteQuicksightUserCollectionTransaction(
                $quicksightUserCollectionTransfer,
                $quicksightUserCollectionResponseTransfer,
            );
        });
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionTransfer $quicksightUserCollectionTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer $quicksightUserCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    protected function executeDeleteQuicksightUserCollectionTransaction(
        QuicksightUserCollectionTransfer $quicksightUserCollectionTransfer,
        QuicksightUserCollectionResponseTransfer $quicksightUserCollectionResponseTransfer
    ): QuicksightUserCollectionResponseTransfer {
        $quicksightUserIdsToDelete = [];
        foreach ($quicksightUserCollectionTransfer->getQuicksightUsers() as $quicksightUserTransfer) {
            $quicksightUserCollectionResponseTransfer->addQuicksightUser($quicksightUserTransfer);

            $quicksightDeleteUserResponseTransfer = $this->amazonQuicksightApiClient->deleteUser($quicksightUserTransfer);
            if ($quicksightDeleteUserResponseTransfer->getErrors()) {
                $quicksightUserCollectionResponseTransfer = $this->addErrorsToQuicksightUserCollectionResponse(
                    $quicksightUserCollectionResponseTransfer,
                    $quicksightDeleteUserResponseTransfer->getErrors(),
                    $quicksightUserTransfer->getIdQuicksightUser(),
                );

                continue;
            }

            $quicksightUserIdsToDelete[] = $quicksightUserTransfer->getIdQuicksightUser();
        }

        $this->amazonQuicksightEntityManager->deleteQuicksightUsers($quicksightUserIdsToDelete);

        return $quicksightUserCollectionResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer $quicksightUserCollectionResponseTransfer
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers
     * @param string $entityIdentifier
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    protected function addErrorsToQuicksightUserCollectionResponse(
        QuicksightUserCollectionResponseTransfer $quicksightUserCollectionResponseTransfer,
        ArrayObject $errorTransfers,
        string $entityIdentifier
    ): QuicksightUserCollectionResponseTransfer {
        foreach ($errorTransfers as $errorTransfer) {
            $errorTransfer->setEntityIdentifier($entityIdentifier);
            $quicksightUserCollectionResponseTransfer->addError($errorTransfer);
        }

        return $quicksightUserCollectionResponseTransfer;
    }
}
