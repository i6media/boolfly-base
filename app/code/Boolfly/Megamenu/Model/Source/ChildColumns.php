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
 * Class ChildColums
 *
 * @package Boolfly\Megamenu\Model\Source
 */
class ChildColumns implements OptionSourceInterface
{

    /**@#%
     *
     * @const
     */
    const ONE_COL = 1;

    const TWO_COL = 2;

    const THREE_COL = 3;

    const FOUR_COL = 4;

    const FIVE_COL = 5;

    const SIX_COL = 6;


    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::ONE_COL => __('1 column'),
            self::TWO_COL => __('2 columns'),
            self::THREE_COL => __('3 columns'),
            self::FOUR_COL => __('4 columns'),
            self::FIVE_COL => __('5 columns'),
            self::SIX_COL => __('6 columns')
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
