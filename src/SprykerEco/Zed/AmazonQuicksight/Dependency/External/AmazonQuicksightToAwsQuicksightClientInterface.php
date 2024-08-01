<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Dependency\External;

use Aws\ResultInterface;

interface AmazonQuicksightToAwsQuicksightClientInterface
{
    /**
     * @param array<string, mixed> $registerUserRequestData
     *
     * @return \Aws\ResultInterface
     */
    public function registerUser(array $registerUserRequestData): ResultInterface;

    /**
     * @param array<string, mixed> $generateEmbedUrlRequestData
     *
     * @return \Aws\ResultInterface
     */
    public function generateEmbedUrlForRegisteredUser(array $generateEmbedUrlRequestData): ResultInterface;
}
