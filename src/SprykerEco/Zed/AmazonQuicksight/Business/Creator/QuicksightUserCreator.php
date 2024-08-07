<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Creator;

use ArrayObject;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightUserCreator implements QuicksightUserCreatorInterface
{
    use TransactionTrait;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface
     */
    protected AmazonQuicksightApiClientInterface $amazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface
     */
    protected AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface $amazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     */
    public function __construct(
        AmazonQuicksightApiClientInterface $amazonQuicksightApiClient,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
    ) {
        $this->amazonQuicksightApiClient = $amazonQuicksightApiClient;
        $this->amazonQuicksightEntityManager = $amazonQuicksightEntityManager;
    }

    /**
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    public function createQuicksightUsersForUserCollectionResponse(
        UserCollectionResponseTransfer $userCollectionResponseTransfer
    ): UserCollectionResponseTransfer {
        $userTransfersWithQuicksightUserRole = $this->extractUserTransfersWithQuicksightUserRole(
            $userCollectionResponseTransfer->getUsers(),
        );

        if ($userTransfersWithQuicksightUserRole === []) {
            return $userCollectionResponseTransfer;
        }

        return $this->getTransactionHandler()->handleTransaction(function () use ($userCollectionResponseTransfer, $userTransfersWithQuicksightUserRole) {
            return $this->executeCreateQuicksightUsersForUsersTransaction(
                $userCollectionResponseTransfer,
                $userTransfersWithQuicksightUserRole,
            );
        });
    }

    /**
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    protected function executeCreateQuicksightUsersForUsersTransaction(
        UserCollectionResponseTransfer $userCollectionResponseTransfer,
        array $userTransfers
    ): UserCollectionResponseTransfer {
        foreach ($userTransfers as $entityIdentifier => $userTransfer) {
            $quicksightUserRegisterResponseTransfer = $this->amazonQuicksightApiClient->registerUser($userTransfer);
            if ($quicksightUserRegisterResponseTransfer->getErrors()->count() !== 0) {
                $userCollectionResponseTransfer = $this->addErrorsToUserCollectionResponse(
                    $userCollectionResponseTransfer,
                    $quicksightUserRegisterResponseTransfer->getErrors(),
                    $entityIdentifier,
                );

                continue;
            }

            $quicksightUserTransfer = $quicksightUserRegisterResponseTransfer->getQuicksightUserOrFail();
            $quicksightUserTransfer->setFkUser($userTransfer->getIdUserOrFail());

            $quicksightUserTransfer = $this->amazonQuicksightEntityManager->createQuicksightUser($quicksightUserTransfer);
            $userTransfer->setQuicksightUser($quicksightUserTransfer);
        }

        return $userCollectionResponseTransfer;
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return array<int|string, \Generated\Shared\Transfer\UserTransfer>
     */
    protected function extractUserTransfersWithQuicksightUserRole(ArrayObject $userTransfers): array
    {
        $extractedUserTransfers = [];
        foreach ($userTransfers as $entityIdentifier => $userTransfer) {
            if ($userTransfer->getQuicksightUser() && $userTransfer->getQuicksightUserOrFail()->getRole()) {
                $extractedUserTransfers[$entityIdentifier] = $userTransfer;
            }
        }

        return $extractedUserTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers
     * @param string $entityIdentifier
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    protected function addErrorsToUserCollectionResponse(
        UserCollectionResponseTransfer $userCollectionResponseTransfer,
        ArrayObject $errorTransfers,
        string $entityIdentifier
    ): UserCollectionResponseTransfer {
        foreach ($errorTransfers as $errorTransfer) {
            $errorTransfer->setEntityIdentifier($entityIdentifier);

            $userCollectionResponseTransfer->addError($errorTransfer);
        }

        return $userCollectionResponseTransfer;
    }
}
