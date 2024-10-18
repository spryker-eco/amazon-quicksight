<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Reader;

use Generated\Shared\Transfer\UserCollectionTransfer;
use Generated\Shared\Transfer\UserConditionsTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToUserFacadeInterface;

class UserReader implements UserReaderInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToUserFacadeInterface
     */
    protected AmazonQuicksightToUserFacadeInterface $userFacade;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToUserFacadeInterface $userFacade
     */
    public function __construct(AmazonQuicksightConfig $amazonQuicksightConfig, AmazonQuicksightToUserFacadeInterface $userFacade)
    {
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->userFacade = $userFacade;
    }

    /**
     * @return \Generated\Shared\Transfer\UserCollectionTransfer
     */
    public function getUsersApplicableForQuicksightUserRegistration(): UserCollectionTransfer
    {
        $userConditionsTransfer = (new UserConditionsTransfer())->setStatuses(
            $this->amazonQuicksightConfig->getUserStatusesApplicableForQuicksightUserRegistration(),
        );
        $userCriteriaTransfer = (new UserCriteriaTransfer())->setUserConditions($userConditionsTransfer);

        return $this->userFacade->getUserCollection($userCriteriaTransfer);
    }
}
