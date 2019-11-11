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

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;

/**
 * Class CmsPage
 *
 * @package Boolfly\Megamenu\Model\Source
 */
class CmsPage implements OptionSourceInterface
{

    /**
     * @var array
     */
    protected $options;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;


    /**
     * CmsPage constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            $collection    = $this->collectionFactory->create();
            /** @var \Magento\Cms\Model\Page $item */
            foreach ($collection as $item) {
                $this->options[$item->getId()] = $item->getTitle();
            }
        }

        return $this->options;
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
