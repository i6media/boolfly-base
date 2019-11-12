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

    /**@#+
     * Cache Tag
     *
     * @const
     */
    const CACHE_TAG = 'megamenu_item';

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
     * @var SubCategories
     */
    private $subCategoryBlock;

    /**
     * Item constructor.
     *
     * @param Template\Context     $context
     * @param MenuInterfaceFactory $menuFactory
     * @param FilterProvider       $filterProvider
     * @param ItemInterface        $menuItem
     * @param array                $data
     */
    public function __construct(
        Template\Context $context,
        MenuInterfaceFactory $menuFactory,
        FilterProvider $filterProvider,
        ItemInterface $menuItem,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->menuFactory    = $menuFactory;
        $this->menuItem       = $menuItem;
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
     * @return SubCategories|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubCategoryBlock()
    {
        if ($this->subCategoryBlock === null) {
            $this->subCategoryBlock = $this->getLayout()->createBlock(SubCategories::class);
        }

        return $this->subCategoryBlock;
    }

    /**
     * Get Sub Categories Html
     *
     * @return string
     */
    public function getSubCategoriesHtml()
    {
        try {
            return $this->getSubCategoryBlock()
                ->setItem($this->getItem())
                ->toHtml();
        } catch (\Exception $e) {
            return '';
        }
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
     * @return array|boolean
     */
    public function groupChildren()
    {
        if (($children = $this->getItem()->getChildren())
            && $this->getContentType() == MainContentType::CHILD_ITEM_TYPE
        ) {
            $groupChildren = [];
            $childCols     = (int)$this->getItem()->getData('main_content_child_columns') ?: 1;
            $totals        = count($children);
            $col           = (int)floor($totals / $childCols);
            $remainder     = $totals % $childCols;
            $temp          = $remainder > 0 ? $col + 1 : $col;
            $group         = 0;
            foreach ($children as $child) {
                if ($temp == 0) {
                    $remainder--;
                    $temp = $remainder > 0 ? $col + 1 : $col;
                    $group++;
                }
                $groupChildren[$group][] = $child;
                $temp--;
            }
            return $groupChildren;
        }

        return false;
    }

    /**
     * @return integer
     */
    private function getContentType()
    {
        return (int)$this->getItem()->getData('main_content_content_type');
    }

    /**
     * Show Content Html
     *
     * @return boolean
     */
    public function showMainContentHtml()
    {
        return $this->getContentType() === MainContentType::CONTENT_TYPE;
    }

    /**
     * Show Subcategories
     *
     * @return boolean
     */
    public function showSubCategories()
    {
        return $this->getContentType() === MainContentType::SUB_CATEGORIES_TYPE;
    }

    /**
     * Get Wysiwyg Content Html
     *
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
     * Get Width Style
     *
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
     * Check Enable
     *
     * @param $type
     * @return boolean
     */
    public function isEnableBlock($type)
    {
        return (boolean)$this->getItem()->getData($type. '_status');
    }

    /**
     * Get Additional Class
     *
     * @return string
     */
    public function getAdditionalClasses()
    {
        /** @var \Boolfly\Megamenu\Model\Menu\Item $item */
        $item  = $this->getItem();
        $class = [];
        if ($item->getData('first')) {
            $class[] = 'first';
        }
        if ($item->getData('last')) {
            $class[] = 'last';
        }
        if ($item->getChildren()) {
            $class[] = 'has-children';
        }

        return join(' ', $class);
    }

    /**
     * Additional class for content
     *
     * @return string
     */
    public function getContentAdditionalClass()
    {
        $item            = $this->getItem();
        $additionalClass = [
            'level' . $item->getLevel(),
            'boolfly-column-' . $item->getData('main_content_child_columns')
        ];

        return implode(' ', $additionalClass);
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
        return $this->getItem()->getIdentities();
    }
}
