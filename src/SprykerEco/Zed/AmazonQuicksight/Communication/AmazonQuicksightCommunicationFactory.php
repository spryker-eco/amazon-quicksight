<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider\QuicksightUserFormDataProvider;
use SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider\QuicksightUserFormDataProviderInterface;
use SprykerEco\Zed\AmazonQuicksight\Communication\Expander\QuicksightUserFormExpander;
use SprykerEco\Zed\AmazonQuicksight\Communication\Expander\QuicksightUserFormExpanderInterface;
use SprykerEco\Zed\AmazonQuicksight\Communication\Transformer\QuicksightUserRoleDataTransformer;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface getEntityManager()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 */
class AmazonQuicksightCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Communication\Expander\QuicksightUserFormExpanderInterface
     */
    public function createQuicksightUserFormExpander(): QuicksightUserFormExpanderInterface
    {
        return new QuicksightUserFormExpander(
            $this->createQuicksightUserRoleDataTransformer(),
            $this->createQuicksightUserFormDataProvider(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Symfony\Component\Form\DataTransformerInterface<array<string, mixed>, array<string, mixed>>
     */
    public function createQuicksightUserRoleDataTransformer(): DataTransformerInterface
    {
        return new QuicksightUserRoleDataTransformer();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider\QuicksightUserFormDataProviderInterface
     */
    public function createQuicksightUserFormDataProvider(): QuicksightUserFormDataProviderInterface
    {
        return new QuicksightUserFormDataProvider($this->getConfig());
    }
}
