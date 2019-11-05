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
 * Class LayoutType
 *
 * @package Boolfly\Megamenu\Model\Source
 */
class LayoutType implements OptionSourceInterface
{

    /**@#%
     *
     * @const
     */
    const HEADER = 1;

    const LEFT_BLOCK = 2;

    const MAIN_CONTENT = 3;

    const RIGHT_BLOCK = 4;

    const BOTTOM_BLOCK = 5;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::HEADER => __('Header'),
            self::LEFT_BLOCK => __('Left Block'),
            self::MAIN_CONTENT => __('Main Content'),
            self::RIGHT_BLOCK => __('Right Block'),
            self::BOTTOM_BLOCK => __('Bottom Block')
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
