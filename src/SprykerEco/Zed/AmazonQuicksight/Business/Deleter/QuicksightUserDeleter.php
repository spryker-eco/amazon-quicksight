<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Deleter;

use ArrayObject;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Matcher\QuicksightUserMatcherInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightUserDeleter implements QuicksightUserDeleterInterface
{
    use TransactionTrait;

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_QUICKSIGHT_USER_DELETION_FAILED = 'The user role for Analytics could not be reset. Please contact your Spryker Success Manager.';

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilterInterface
     */
    protected UserCollectionFilterInterface $userCollectionFilter;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Matcher\QuicksightUserMatcherInterface
     */
    protected QuicksightUserMatcherInterface $quicksightUserMatcher;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface
     */
    protected AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface
     */
    protected AmazonQuicksightApiClientInterface $amazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface
     */
    protected AmazonQuicksightToMessengerFacadeInterface $messengerFacade;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilterInterface $userCollectionFilter
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Matcher\QuicksightUserMatcherInterface $quicksightUserMatcher
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface $amazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface $messengerFacade
     */
    public function __construct(
        UserCollectionFilterInterface $userCollectionFilter,
        QuicksightUserMatcherInterface $quicksightUserMatcher,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager,
        AmazonQuicksightApiClientInterface $amazonQuicksightApiClient,
        AmazonQuicksightToMessengerFacadeInterface $messengerFacade
    ) {
        $this->userCollectionFilter = $userCollectionFilter;
        $this->quicksightUserMatcher = $quicksightUserMatcher;
        $this->amazonQuicksightEntityManager = $amazonQuicksightEntityManager;
        $this->amazonQuicksightApiClient = $amazonQuicksightApiClient;
        $this->messengerFacade = $messengerFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    public function deleteQuicksightUsersByUserCollectionResponse(
        UserCollectionResponseTransfer $userCollectionResponseTransfer
    ): UserCollectionResponseTransfer {
        $filteredUserTransfers = $this->userCollectionFilter->filterOutUserTransfersNotApplicableForQuicksightUserDeletion(
            $userCollectionResponseTransfer->getUsers()->getArrayCopy(),
        );

        if ($filteredUserTransfers === []) {
            return $userCollectionResponseTransfer;
        }

        return $this->getTransactionHandler()->handleTransaction(function () use ($userCollectionResponseTransfer, $filteredUserTransfers) {
            return $this->executeDeleteQuicksightUsersByUserCollectionResponseTransaction(
                $userCollectionResponseTransfer,
                $filteredUserTransfers,
            );
        });
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function deleteNotMatchedQuicksightUsers(): QuicksightUserCollectionResponseTransfer
    {
        $quicksightUserCollectionResponseTransfer = new QuicksightUserCollectionResponseTransfer();

        $quicksightListUsersResponseTransfer = $this->amazonQuicksightApiClient->listUsers();
        if ($quicksightListUsersResponseTransfer->getErrors()->count() !== 0) {
            return $quicksightUserCollectionResponseTransfer->setErrors(
                $quicksightListUsersResponseTransfer->getErrors(),
            );
        }

        $quicksightUserTransfers = $quicksightListUsersResponseTransfer->getQuicksightUsers();
        if ($quicksightUserTransfers->count() === 0) {
            return $quicksightUserCollectionResponseTransfer;
        }

        $notMatchedQuicksightUserTransfers = $this->quicksightUserMatcher->getQuicksightUsersNotMatchedWithExistingUsers(
            $quicksightUserTransfers,
        );

        foreach ($notMatchedQuicksightUserTransfers as $quicksightUserTransfer) {
            $quicksightUserCollectionResponseTransfer = $this->deleteQuicksightUser(
                $quicksightUserTransfer,
                $quicksightUserCollectionResponseTransfer,
            );
        }

        return $quicksightUserCollectionResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    protected function executeDeleteQuicksightUsersByUserCollectionResponseTransaction(
        UserCollectionResponseTransfer $userCollectionResponseTransfer,
        array $userTransfers
    ): UserCollectionResponseTransfer {
        $userIdsToDelete = [];
        foreach ($userTransfers as $entityIdentifier => $userTransfer) {
            $quicksightDeleteUserResponseTransfer = $this->amazonQuicksightApiClient->deleteUserByUsername($userTransfer);
            if ($quicksightDeleteUserResponseTransfer->getErrors()->count() !== 0) {
                $userCollectionResponseTransfer = $this->addErrorsToUserCollectionResponse(
                    $userCollectionResponseTransfer,
                    $quicksightDeleteUserResponseTransfer->getErrors(),
                    (string)$entityIdentifier,
                );

                $this->messengerFacade->addInfoMessage(
                    (new MessageTransfer())->setValue(static::ERROR_MESSAGE_QUICKSIGHT_USER_DELETION_FAILED),
                );

                continue;
            }

            $userIdsToDelete[] = $userTransfer->getIdUserOrFail();
        }

        $this->amazonQuicksightEntityManager->deleteQuicksightUsersByUserIds($userIdsToDelete);

        return $userCollectionResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer $quicksightUserCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    protected function deleteQuicksightUser(
        QuicksightUserTransfer $quicksightUserTransfer,
        QuicksightUserCollectionResponseTransfer $quicksightUserCollectionResponseTransfer
    ): QuicksightUserCollectionResponseTransfer {
        $quicksightDeleteUserResponseTransfer = $this->amazonQuicksightApiClient->deleteUserByPrincipalId(
            $quicksightUserTransfer,
        );

        if ($quicksightDeleteUserResponseTransfer->getErrors()->count() !== 0) {
            return $this->addErrorsToQuicksightUserCollectionResponse(
                $quicksightUserCollectionResponseTransfer,
                $quicksightDeleteUserResponseTransfer->getErrors(),
            );
        }

        $quicksightUserCollectionResponseTransfer->addQuicksightUser($quicksightUserTransfer);

        return $quicksightUserCollectionResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer $quicksightUserCollectionResponseTransfer
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    protected function addErrorsToQuicksightUserCollectionResponse(
        QuicksightUserCollectionResponseTransfer $quicksightUserCollectionResponseTransfer,
        ArrayObject $errorTransfers
    ): QuicksightUserCollectionResponseTransfer {
        foreach ($errorTransfers as $errorTransfer) {
            $quicksightUserCollectionResponseTransfer->addError($errorTransfer);
        }

        return $quicksightUserCollectionResponseTransfer;
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
