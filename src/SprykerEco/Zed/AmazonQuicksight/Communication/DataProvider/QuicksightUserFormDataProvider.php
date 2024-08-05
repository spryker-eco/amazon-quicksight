<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider;

use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;

class QuicksightUserFormDataProvider implements QuicksightUserFormDataProviderInterface
{
 /**
  * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
  */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     */
    public function __construct(AmazonQuicksightConfig $amazonQuicksightConfig)
    {
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
    }

    /**
     * @return array<string, string>
     */
    public function getQuicksightUserRoleChoices(): array
    {
        $quicksightUserRoles = $this->amazonQuicksightConfig->getQuicksightUserRoles();

        return array_combine(
            $this->formatQuicksightUserRoleChoiceLabels($quicksightUserRoles),
            $quicksightUserRoles,
        );
    }

    /**
     * @param list<string> $quicksightUserRoles
     *
     * @return list<string>
     */
    protected function formatQuicksightUserRoleChoiceLabels(array $quicksightUserRoles): array
    {
        return array_map(function ($quicksightUserRole) {
            return ucfirst(strtolower($quicksightUserRole));
        }, $quicksightUserRoles);
    }
}
