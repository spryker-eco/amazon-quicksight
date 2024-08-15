<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Expander;

use Generated\Shared\Transfer\AnalyticsCollectionTransfer;
use Generated\Shared\Transfer\AnalyticsRequestTransfer;
use Generated\Shared\Transfer\AnalyticsTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;
use Twig\Environment;

class AnalyticsExpander implements AnalyticsExpanderInterface
{
    /**
     * @var string
     */
    protected const TEMPLATE_PATH_QUICKSIGHT_ANALYTICS = '@AmazonQuicksight/_partials/quicksight-analytics.twig';

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface
     */
    protected AmazonQuicksightRepositoryInterface $amazonQuicksightRepository;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface
     */
    protected AmazonQuicksightApiClientInterface $amazonQuicksightApiClient;

    /**
     * @var \Twig\Environment
     */
    protected Environment $twigEnvironment;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface $amazonQuicksightRepository
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface $amazonQuicksightApiClient
     * @param \Twig\Environment $twigEnvironment
     */
    public function __construct(
        AmazonQuicksightRepositoryInterface $amazonQuicksightRepository,
        AmazonQuicksightApiClientInterface $amazonQuicksightApiClient,
        Environment $twigEnvironment
    ) {
        $this->amazonQuicksightRepository = $amazonQuicksightRepository;
        $this->amazonQuicksightApiClient = $amazonQuicksightApiClient;
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @param \Generated\Shared\Transfer\AnalyticsRequestTransfer $analyticsRequestTransfer
     * @param \Generated\Shared\Transfer\AnalyticsCollectionTransfer $analyticsCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsCollectionTransfer
     */
    public function expandAnalyticsCollectionWithQuicksightAnalytics(
        AnalyticsRequestTransfer $analyticsRequestTransfer,
        AnalyticsCollectionTransfer $analyticsCollectionTransfer
    ): AnalyticsCollectionTransfer {
        $userTransfer = $analyticsRequestTransfer->getUserOrFail();
        $quicksightUserTransfer = $this->findQuicksightUser($userTransfer);

        if (!$quicksightUserTransfer) {
            return $analyticsCollectionTransfer;
        }
//        $this->amazonQuicksightApiClient->startAssetBundleExportJob($quicksightUserTransfer);
        $quicksightGenerateEmbedUrlResponseTransfer = $this->amazonQuicksightApiClient->generateEmbedUrlForRegisteredUser(
            $quicksightUserTransfer,
        );
//dd($quicksightGenerateEmbedUrlResponseTransfer->getEmbedUrl()->getUrl());
        $content = $this->twigEnvironment->render(
            static::TEMPLATE_PATH_QUICKSIGHT_ANALYTICS,
            [
                'quicksightGenerateEmbedUrlResponse' => $quicksightGenerateEmbedUrlResponseTransfer,
            ],
        );

        $analyticsCollectionTransfer->addAnalytics((new AnalyticsTransfer())->setContent($content));

        return $analyticsCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer|null
     */
    protected function findQuicksightUser(UserTransfer $userTransfer): ?QuicksightUserTransfer
    {
        $quicksightUserTransfers = $this->amazonQuicksightRepository
            ->getQuicksightUsersByUserIds([$userTransfer->getIdUserOrFail()]);

        if (!isset($quicksightUserTransfers[0])) {
            return null;
        }

        return $quicksightUserTransfers[0];
    }
}
