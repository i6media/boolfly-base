<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Megamenu
 */
namespace Boolfly\Megamenu\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MainContentType
 *
 * @package Boolfly\Megamenu\Model\Source
 */
class MainContentType implements OptionSourceInterface
{

    /**@#%
     *
     * @const
     */
    const CHILD_ITEM_TYPE = 1;

    const CONTENT_TYPE = 2;

    const SUB_CATEGORIES_TYPE = 3;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::CHILD_ITEM_TYPE => __('Child Menu Item'),
            self::CONTENT_TYPE => __('Content'),
            self::SUB_CATEGORIES_TYPE => __('Sub-Categories')
        ];
    }

    /**
     * @return array|string[]
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }
        array_unshift($result, ['value' => '', 'label' => 'Select...']);

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
