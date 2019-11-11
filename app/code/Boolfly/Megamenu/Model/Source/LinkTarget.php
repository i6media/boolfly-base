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
 * Class LinkTarget
 *
 * @package Boolfly\Megamenu\Model\Source
 */
class LinkTarget implements OptionSourceInterface
{

    /**@#%
     *
     * @const
     */
    const BLANK_TYPE = '_blank';

    const SELF_TYPE = '_self';

    const PARENT_TYPE = '_parent';

    const TOP_TYPE = '_top';


    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::BLANK_TYPE => __('Load in a new window'),
            self::SELF_TYPE => __('Load in the same frame as it was clicked'),
            self::PARENT_TYPE => __('Load in the parent frameset'),
            self::TOP_TYPE => __('Load in the full body of the window')
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
