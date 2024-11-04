<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Deleter;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Zed\AmazonQuicksight\Business\Adder\ErrorAdderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface;
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
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface
     */
    protected UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface
     */
    protected AmazonQuicksightToMessengerFacadeInterface $messengerFacade;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Adder\ErrorAdderInterface
     */
    protected ErrorAdderInterface $errorAdder;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilterInterface $userCollectionFilter
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Matcher\QuicksightUserMatcherInterface $quicksightUserMatcher
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface $messengerFacade
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Adder\ErrorAdderInterface $errorAdder
     */
    public function __construct(
        UserCollectionFilterInterface $userCollectionFilter,
        QuicksightUserMatcherInterface $quicksightUserMatcher,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager,
        UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient,
        AmazonQuicksightToMessengerFacadeInterface $messengerFacade,
        ErrorAdderInterface $errorAdder
    ) {
        $this->userCollectionFilter = $userCollectionFilter;
        $this->quicksightUserMatcher = $quicksightUserMatcher;
        $this->amazonQuicksightEntityManager = $amazonQuicksightEntityManager;
        $this->userAmazonQuicksightApiClient = $userAmazonQuicksightApiClient;
        $this->messengerFacade = $messengerFacade;
        $this->errorAdder = $errorAdder;
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
            $quicksightDeleteUserResponseTransfer = $this->userAmazonQuicksightApiClient->deleteUserByUsername($userTransfer);
            if ($quicksightDeleteUserResponseTransfer->getErrors()->count() !== 0) {
                $userCollectionResponseTransfer = $this->errorAdder->addErrorsToUserCollectionResponse(
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
}
