<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence;

use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUserQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper\QuicksightUserMapper;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface getEntityManager()
 */
class AmazonQuicksightPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper\QuicksightUserMapper
     */
    public function createQuicksightUserMapper(): QuicksightUserMapper
    {
        return new QuicksightUserMapper();
    }

    /**
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUserQuery
     */
    public function getQuicksightUserQuery(): SpyQuicksightUserQuery
    {
        return SpyQuicksightUserQuery::create();
    }
}
