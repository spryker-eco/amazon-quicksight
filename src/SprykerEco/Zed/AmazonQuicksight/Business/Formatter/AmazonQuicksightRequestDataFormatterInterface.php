<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Formatter;

interface AmazonQuicksightRequestDataFormatterInterface
{
    /**
     * @param array<string, mixed> $requestData
     *
     * @return array<string, mixed>
     */
    public function formatRequestData(array $requestData): array;
}
