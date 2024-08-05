<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider;

interface QuicksightUserFormDataProviderInterface
{
    /**
     * @return array<string, string>
     */
    public function getQuicksightUserRoleChoices(): array;
}
