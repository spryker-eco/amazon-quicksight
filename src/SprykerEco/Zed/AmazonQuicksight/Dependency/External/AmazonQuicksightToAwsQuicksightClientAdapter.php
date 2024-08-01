<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Dependency\External;

use Aws\QuickSight\QuickSightClient;
use Aws\ResultInterface;

class AmazonQuicksightToAwsQuicksightClientAdapter implements AmazonQuicksightToAwsQuicksightClientInterface
{
    /**
     * @var \Aws\QuickSight\QuickSightClient
     */
    protected $quicksightClient;

    /**
     * @param array<string, mixed> $args
     */
    public function __construct(array $args)
    {
        $this->quicksightClient = new QuickSightClient($args);
    }

    /**
     * @param array<string, mixed> $registerUserRequestData
     *
     * @return \Aws\ResultInterface
     */
    public function registerUser(array $registerUserRequestData): ResultInterface
    {
        return $this->quicksightClient->registerUser($registerUserRequestData);
    }

    /**
     * @param array<string, mixed> $generateEmbedUrlRequestData
     *
     * @return \Aws\ResultInterface
     */
    public function generateEmbedUrlForRegisteredUser(array $generateEmbedUrlRequestData): ResultInterface
    {
        return $this->quicksightClient->generateEmbedUrlForRegisteredUser($generateEmbedUrlRequestData);
    }
}
