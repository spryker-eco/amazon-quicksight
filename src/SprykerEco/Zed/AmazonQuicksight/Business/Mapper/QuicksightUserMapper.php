<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Mapper;

use Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;

class QuicksightUserMapper implements QuicksightUserMapperInterface
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
    ): QuicksightUserRegisterRequestTransfer {
        return $quicksightUserRegisterRequestTransfer
            ->setEmail($userTransfer->getUsernameOrFail())
            ->setUserName($userTransfer->getUsernameOrFail())
            ->setUserRole(strtoupper($userTransfer->getQuicksightUserOrFail()->getRoleOrFail()));
    }

    /**
     * @param array<string, mixed> $quicksightUserData
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function mapQuicksightUserDataToQuicksightUserTransfer(
        array $quicksightUserData,
        QuicksightUserTransfer $quicksightUserTransfer
    ): QuicksightUserTransfer {
        return $quicksightUserTransfer->fromArray($quicksightUserData, true);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     * @param \Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer $quicksightDeleteUserRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer
     */
    public function mapQuicksightUserTransferToQuicksightDeleteUserRequestTransfer(
        QuicksightUserTransfer $quicksightUserTransfer,
        QuicksightDeleteUserRequestTransfer $quicksightDeleteUserRequestTransfer
    ): QuicksightDeleteUserRequestTransfer {
        return $quicksightDeleteUserRequestTransfer->fromArray($quicksightUserTransfer->toArray(), true);
    }
}
