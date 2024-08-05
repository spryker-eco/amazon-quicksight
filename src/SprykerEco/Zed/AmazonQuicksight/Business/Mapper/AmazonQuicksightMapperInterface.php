<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Mapper;

use Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;

interface AmazonQuicksightMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer $quicksightUserRegisterRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer
     */
    public function mapUserTransferToQuicksightUserRegisterRequestTransfer(
        UserTransfer $userTransfer,
        QuicksightUserRegisterRequestTransfer $quicksightUserRegisterRequestTransfer
    ): QuicksightUserRegisterRequestTransfer;

    /**
     * @param array<string, mixed> $quicksightUserData
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function mapQuicksightUserDataToQuicksightUserTransfer(
        array $quicksightUserData,
        QuicksightUserTransfer $quicksightUserTransfer
    ): QuicksightUserTransfer;
}
