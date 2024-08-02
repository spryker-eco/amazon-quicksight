<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientAdapter;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToTranslatorFacadeBridge;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 */
class AmazonQuicksightDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const AWS_QUICKSIGHT_CLIENT = 'AWS_QUICKSIGHT_CLIENT';

    /**
     * @var string
     */
    public const FACADE_TRANSLATOR = 'FACADE_TRANSLATOR';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addAwsQuicksightClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);
        $container = $this->addTranslatorFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAwsQuicksightClient(Container $container): Container
    {
        $container->set(static::AWS_QUICKSIGHT_CLIENT, function () {
            return new AmazonQuicksightToAwsQuicksightClientAdapter(
                $this->getConfig()->getQuicksightClientConfiguration(),
            );
        });

        return $container;
    }

    /**
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTranslatorFacade(Container $container): Container
    {
        $container->set(static::FACADE_TRANSLATOR, function (Container $container) {
            return new AmazonQuicksightToTranslatorFacadeBridge($container->getLocator()->translator()->facade());
        });

        return $container;
    }
}
