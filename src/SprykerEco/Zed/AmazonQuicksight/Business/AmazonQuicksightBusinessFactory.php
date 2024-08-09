<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClient;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Checker\QuicksightAnalyticsEmbedUrlProviderChecker;
use SprykerEco\Zed\AmazonQuicksight\Business\Checker\QuicksightAnalyticsEmbedUrlProviderCheckerInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreator;
use SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Expander\UserExpander;
use SprykerEco\Zed\AmazonQuicksight\Business\Expander\UserExpanderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatter;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapper;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Provider\AnalyticsEmbedUrlProvider;
use SprykerEco\Zed\AmazonQuicksight\Business\Provider\AnalyticsEmbedUrlProviderInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface getEntityManager()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 */
class AmazonQuicksightBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Expander\UserExpanderInterface
     */
    public function createUserExpander(): UserExpanderInterface
    {
        return new UserExpander($this->getRepository());
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreatorInterface
     */
    public function createQuicksightUserCreator(): QuicksightUserCreatorInterface
    {
        return new QuicksightUserCreator(
            $this->getEntityManager(),
            $this->createAmazonQuicksightApiClient(),
            $this->getMessengerFacade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface
     */
    public function createAmazonQuicksightApiClient(): AmazonQuicksightApiClientInterface
    {
        return new AmazonQuicksightApiClient(
            $this->getConfig(),
            $this->createAmazonQuicksightMapper(),
            $this->createAmazonQuicksightRequestDataFormatter(),
            $this->getAwsQuicksightClient(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface
     */
    public function createAmazonQuicksightMapper(): AmazonQuicksightMapperInterface
    {
        return new AmazonQuicksightMapper();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface
     */
    public function createAmazonQuicksightRequestDataFormatter(): AmazonQuicksightRequestDataFormatterInterface
    {
        return new AmazonQuicksightRequestDataFormatter();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Checker\QuicksightAnalyticsEmbedUrlProviderCheckerInterface
     */
    public function createQuicksightAnalyticsEmbedUrlProviderChecker(): QuicksightAnalyticsEmbedUrlProviderCheckerInterface
    {
        return new QuicksightAnalyticsEmbedUrlProviderChecker($this->getRepository());
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Provider\AnalyticsEmbedUrlProviderInterface
     */
    public function createAnalyticsEmbedUrlProvider(): AnalyticsEmbedUrlProviderInterface
    {
        return new AnalyticsEmbedUrlProvider(
            $this->createAmazonQuicksightApiClient(),
            $this->createAmazonQuicksightMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface
     */
    public function getAwsQuicksightClient(): AmazonQuicksightToAwsQuicksightClientInterface
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT);
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface
     */
    public function getMessengerFacade(): AmazonQuicksightToMessengerFacadeInterface
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::FACADE_MESSENGER);
    }
}
