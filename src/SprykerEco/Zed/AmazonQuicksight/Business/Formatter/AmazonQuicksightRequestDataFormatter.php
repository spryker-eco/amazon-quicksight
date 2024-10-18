<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Formatter;

class AmazonQuicksightRequestDataFormatter implements AmazonQuicksightRequestDataFormatterInterface
{
    /**
     * @param array<string, mixed> $requestData
     *
     * @return array<string, mixed>
     */
    public function formatRequestData(array $requestData): array
    {
        $formattedData = [];
        foreach ($requestData as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_array($value)) {
                $value = $this->formatRequestData($value);
            }

            $formattedData[ucfirst($key)] = $value;
        }

        return $formattedData;
    }
}
