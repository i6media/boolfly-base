<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Banner Slider
 */
namespace Boolfly\BannerSlider\Helper;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Boolfly\BannerSlider\Model\ImageField;

/**
 * Class RedundantImageChecker
 *
 * @package Boolfly\BannerSlider\Helper
 */
class RedundantImageChecker
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * RedundantImageChecker constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
         ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * Checking Image Unused
     *
     * @param $image
     * @return boolean
     */
    public function checkImageUnused($image)
    {
        $connection = $this->connection;
        $conditionArray = [];
        foreach (ImageField::getField() as $field) {
            $conditionArray[] = $connection->quoteInto($field . ' = ?', $image);
        }
        $conditions = join(' OR ', $conditionArray);
        $select = $connection->select()
            ->from($connection->getTableName('bf_banner'), 'banner_id')
            ->where($conditions)
            ->limit(1);

        return ((int)$connection->fetchOne($select)) < 1;
    }
}