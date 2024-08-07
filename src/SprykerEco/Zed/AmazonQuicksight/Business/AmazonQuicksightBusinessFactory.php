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
use SprykerEco\Zed\AmazonQuicksight\Business\Expander\UserExpander;
use SprykerEco\Zed\AmazonQuicksight\Business\Expander\UserExpanderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatter;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\QuicksightUserMapper;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\QuicksightUserMapperInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;

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
            $this->createQuicksightUserMapper(),
            $this->createAmazonQuicksightRequestDataFormatter(),
            $this->getAwsQuicksightClient(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\QuicksightUserMapperInterface
     */
    public function createQuicksightUserMapper(): QuicksightUserMapperInterface
    {
        return new QuicksightUserMapper();
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
