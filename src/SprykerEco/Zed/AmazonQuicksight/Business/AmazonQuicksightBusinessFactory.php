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
use SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreator;
use SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatter;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapper;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface getEntityManager()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 */
class AmazonQuicksightBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightUserCreatorInterface
     */
    public function createQuicksightUserCreator(): QuicksightUserCreatorInterface
    {
        return new QuicksightUserCreator(
            $this->createAmazonQuicksightApiClient(),
            $this->getEntityManager(),
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
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface
     */
    public function getAwsQuicksightClient(): AmazonQuicksightToAwsQuicksightClientInterface
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT);
    }
}
