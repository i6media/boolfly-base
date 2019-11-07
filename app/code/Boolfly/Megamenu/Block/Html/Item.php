<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Mega Menu
 */
namespace Boolfly\Megamenu\Block\Html;

use Boolfly\Megamenu\Api\Data\ItemInterface;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Boolfly\Megamenu\Api\Data\MenuInterfaceFactory;
use Boolfly\Megamenu\Model\Source\LayoutType;
use Boolfly\Megamenu\Model\Source\MainContentType;

/**
 * Item block
 *
 * @api
 * @since 100.0.2
 */
class Item extends Template implements IdentityInterface
{

    /**
     * @var MenuInterfaceFactory
     */
    private $menuFactory;

    /**
     * @var \Boolfly\Megamenu\Model\Menu\Item
     */
    protected $menuItem;

    /**
     * @var string
     */
    protected $menuContentTemplate = 'Boolfly_Megamenu::html/content.phtml';

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * Item constructor.
     *
     * @param Template\Context $context
     * @param MenuInterfaceFactory $menuFactory
     * @param FilterProvider $filterProvider
     * @param ItemInterface $menuItem
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        MenuInterfaceFactory $menuFactory,
        FilterProvider $filterProvider,
        ItemInterface $menuItem,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->menuFactory = $menuFactory;
        $this->menuItem = $menuItem;
        $this->filterProvider = $filterProvider;
    }

    /**
     * @param $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->menuItem->setData($item);
        return $this;
    }

    /**
     * Get Item
     *
     * @return \Boolfly\Megamenu\Model\Menu\Item
     */
    public function getItem()
    {
        return $this->menuItem;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if ($this->getItem()->getId()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getContentHtml()
    {
        $html = '';
        foreach ($this->getLayoutTemplate() as $type => $template) {
            $html .= $this->setTemplate($template)->toHtml();
        }

        return $html;
    }

    /**
     * List Layout Template
     *
     * @return array
     */
    private function getLayoutTemplate()
    {
        return [
            LayoutType::HEADER => 'Boolfly_Megamenu::html/content/header.phtml',
            LayoutType::LEFT_BLOCK => 'Boolfly_Megamenu::html/content/left.phtml',
            LayoutType::MAIN_CONTENT => 'Boolfly_Megamenu::html/content/main.phtml',
            LayoutType::RIGHT_BLOCK => 'Boolfly_Megamenu::html/content/right.phtml',
            LayoutType::BOTTOM_BLOCK => 'Boolfly_Megamenu::html/content/bottom.phtml',
        ];
    }

    /**
     * Group children by config columns
     *
     * @return array|bool
     */
    public function groupChildren()
    {
        if ($children = $this->getItem()->getChildren()) {
            $groupChildren = [];
            $contentType = (int)$this->getItem()->getData('main_content_content_type');
            $col = (int)$this->getItem()->getData('main_content_child_columns');

            if ($contentType !== MainContentType::CONTENT_TYPE) {
                if (!$col) {
                    $col = 1;
                }
                $numberCol = (int)ceil(count($children) / $col);
                $temp = $numberCol;
                $group = 0;
                foreach ($children as $child) {
                    if ($temp == 0) {
                        $temp = $numberCol;
                        $group++;
                    }
                    $groupChildren[$group][] = $child;
                    $temp--;
                }
                return $groupChildren;
            }
        }

        return false;
    }

    /**
     * Show Content Html
     *
     * @return bool
     */
    public function showMainContentHtml()
    {
        $contentType = (int)$this->getItem()->getData('main_content_content_type');

        return $contentType === MainContentType::CONTENT_TYPE;
    }

    /**
     * @param $type
     * @return string
     */
    public function getWysiwygContent($type)
    {
        $key = $type . '_content';
        if ($rawContent = $this->getItem()->getData($key)) {
            try {
                return $this->filterProvider
                    ->getBlockFilter()
                    ->filter($rawContent);
            } catch (\Exception $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * @param $type
     * @return string
     */
    public function getWidthStyle($type)
    {
        $key = $type . '_width';
        if ($width = trim($this->getItem()->getData($key))) {
            return 'width: '. rtrim($width, '%') . '%;';
        }
        return '';
    }

    /**
     * Is Enable Block
     *
     * @param $type
     * @return bool
     */
    public function isEnableBlock($type)
    {
        return (boolean)$this->getItem()->getData($type. '_status');
    }

    /**
     * @return string
     */
    public function getAdditionalClass()
    {
        $item = $this->getItem();
        $level = 'level' . $item->getLevel();
        $colClass = 'bf-column-' . $item->getData('main_content_child_columns');

        return $level . ' ' . $colClass . ' ';
    }

    /**
     * @param array $item
     * @return string
     */
    public function getRecursiveContentHtml($item)
    {
        $this->setItem($item);
        $this->setTemplate($this->menuContentTemplate);
        return $this->toHtml();
    }


    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        // TODO: Implement getIdentities() method.
        return [];
    }

}