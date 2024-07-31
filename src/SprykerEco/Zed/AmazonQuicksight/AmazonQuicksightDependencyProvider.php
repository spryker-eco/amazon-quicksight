<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientAdapter;

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
    protected function addAwsQuicksightClient(Container $container): Container
    {
        $container->set(static::AWS_QUICKSIGHT_CLIENT, function () {
            return new AmazonQuicksightToAwsQuicksightClientAdapter(
                $this->getConfig()->getQuicksightClientConfiguration(),
            );
        });

        return $container;
    }
}
