<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\ApiClient;

use Generated\Shared\Transfer\QuicksightDeleteUserResponseTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\QuicksightListUsersResponseTransfer;
use Generated\Shared\Transfer\QuicksightUpdateUserResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;

interface UserAmazonQuicksightApiClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserRegisterResponseTransfer
     */
    public function registerUser(UserTransfer $userTransfer): QuicksightUserRegisterResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer
     */
    public function generateEmbedUrlForRegisteredUser(
        QuicksightUserTransfer $quicksightUserTransfer
    ): QuicksightGenerateEmbedUrlResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteUserResponseTransfer
     */
    public function deleteUserByPrincipalId(QuicksightUserTransfer $quicksightUserTransfer): QuicksightDeleteUserResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteUserResponseTransfer
     */
    public function deleteUserByUsername(UserTransfer $userTransfer): QuicksightDeleteUserResponseTransfer;

    /**
     * @return \Generated\Shared\Transfer\QuicksightListUsersResponseTransfer
     */
    public function listUsers(): QuicksightListUsersResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUpdateUserResponseTransfer
     */
    public function updateUser(UserTransfer $userTransfer): QuicksightUpdateUserResponseTransfer;
}
