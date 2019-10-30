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
 * Class DesktopTemplate
 *
 * @package Boolfly\Megamenu\Model\Source
 */
class DesktopTemplate implements OptionSourceInterface
{

    /**@#%
     *
     * @const
     */
    const VERTICAL_LEFT = 'vertical_left';

    const VERTICAL_RIGHT = 'vertical_right';

    const HORIZONTAL = 'horizontal';


    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::VERTICAL_LEFT => __('Vertical Left'),
            self::VERTICAL_RIGHT => __('Vertical Right'),
            self::HORIZONTAL => __('Horizontal')
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