<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Creator;

use ArrayObject;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Zed\AmazonQuicksight\Business\Adder\ErrorAdderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Matcher\QuicksightUserMatcherInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightUserCreator implements QuicksightUserCreatorInterface
{
    use TransactionTrait;

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_QUICKSIGHT_USER_REGISTRATION_FAILED = 'The user role for Analytics could not be set. Please try again later.';

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
    public function createQuicksightUsersByUserCollectionResponse(
        UserCollectionResponseTransfer $userCollectionResponseTransfer
    ): UserCollectionResponseTransfer {
        $filteredUserTransfers = $this->userCollectionFilter->filterOutUserTransfersNotApplicableForQuicksightUserRegistration(
            $userCollectionResponseTransfer->getUsers()->getArrayCopy(),
        );

        if ($filteredUserTransfers === []) {
            return $userCollectionResponseTransfer;
        }

        return $this->getTransactionHandler()->handleTransaction(function () use ($userCollectionResponseTransfer, $filteredUserTransfers) {
            return $this->executeCreateQuicksightUsersForUserCollectionResponseTransaction(
                $userCollectionResponseTransfer,
                $filteredUserTransfers,
            );
        });
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function createMatchedQuicksightUsers(): QuicksightUserCollectionResponseTransfer
    {
        $quicksightUserCollectionResponseTransfer = new QuicksightUserCollectionResponseTransfer();

        $quicksightListUsersResponseTransfer = $this->userAmazonQuicksightApiClient->listUsers();
        if ($quicksightListUsersResponseTransfer->getErrors()->count() !== 0) {
            return $quicksightUserCollectionResponseTransfer->setErrors(
                $quicksightListUsersResponseTransfer->getErrors(),
            );
        }

        $quicksightUserTransfers = $quicksightListUsersResponseTransfer->getQuicksightUsers();
        if ($quicksightUserTransfers->count() === 0) {
            return $quicksightUserCollectionResponseTransfer;
        }

        $matchedQuicksightUserTransfers = $this->quicksightUserMatcher->getQuicksightUsersMatchedWithExistingUsers(
            $quicksightUserTransfers,
        );
        if ($matchedQuicksightUserTransfers === []) {
            return $quicksightUserCollectionResponseTransfer;
        }

        $persistedQuicksightUserTransfers = $this->getTransactionHandler()->handleTransaction(function () use ($matchedQuicksightUserTransfers) {
            return $this->executeCreateQuicksightUsersForRegisteredQuicksightUsersMatchedExistingUsersTransaction($matchedQuicksightUserTransfers);
        });

        return $quicksightUserCollectionResponseTransfer->setQuicksightUsers(new ArrayObject($persistedQuicksightUserTransfers));
    }

    /**
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    protected function executeCreateQuicksightUsersForUserCollectionResponseTransaction(
        UserCollectionResponseTransfer $userCollectionResponseTransfer,
        array $userTransfers
    ): UserCollectionResponseTransfer {
        foreach ($userTransfers as $entityIdentifier => $userTransfer) {
            $quicksightUserRegisterResponseTransfer = $this->userAmazonQuicksightApiClient->registerUser($userTransfer);
            if ($quicksightUserRegisterResponseTransfer->getErrors()->count() !== 0) {
                $userCollectionResponseTransfer = $this->errorAdder->addErrorsToUserCollectionResponse(
                    $userCollectionResponseTransfer,
                    $quicksightUserRegisterResponseTransfer->getErrors(),
                    (string)$entityIdentifier,
                );

                $this->messengerFacade->addErrorMessage(
                    (new MessageTransfer())->setValue(static::ERROR_MESSAGE_QUICKSIGHT_USER_REGISTRATION_FAILED),
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
     * @param array<int|string, \Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return array<int|string, \Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    protected function executeCreateQuicksightUsersForRegisteredQuicksightUsersMatchedExistingUsersTransaction(
        array $quicksightUserTransfers
    ): array {
        foreach ($quicksightUserTransfers as $quicksightUserTransfer) {
            $this->amazonQuicksightEntityManager->createQuicksightUser($quicksightUserTransfer);
        }

        return $quicksightUserTransfers;
    }
}
