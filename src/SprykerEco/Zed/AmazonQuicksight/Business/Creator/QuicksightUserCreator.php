<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Creator;

use ArrayObject;
use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;
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
