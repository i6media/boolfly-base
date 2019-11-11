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
use Boolfly\Megamenu\Model\Menu\Item;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Boolfly\Megamenu\Api\Data\MenuInterfaceFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\CategoryFactory;

/**
 * Sub Categories block
 *
 * @api
 * @since 100.0.2
 */
class SubCategories extends Template implements IdentityInterface
{

    /**
     * @const
     */
    const CACHE_TAG = 'megamenu_sub_categories';

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
    protected $_template = 'Boolfly_Megamenu::html/content/sub_categories.phtml';

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @var \Magento\Catalog\Model\Category[]
     */
    private $groupCategory;

    /**
     * @var CategoryCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    private $currentCategory;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var int
     */
    private $level;

    /**
     * SubCategories constructor.
     *
     * @param Template\Context          $context
     * @param MenuInterfaceFactory      $menuFactory
     * @param FilterProvider            $filterProvider
     * @param ItemInterface             $menuItem
     * @param CategoryCollectionFactory $collectionFactory
     * @param CategoryFactory           $categoryFactory
     * @param array                     $data
     */
    public function __construct(
        Template\Context $context,
        MenuInterfaceFactory $menuFactory,
        FilterProvider $filterProvider,
        ItemInterface $menuItem,
        CategoryCollectionFactory $collectionFactory,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->menuFactory       = $menuFactory;
        $this->menuItem          = $menuItem;
        $this->filterProvider    = $filterProvider;
        $this->collectionFactory = $collectionFactory;
        $this->categoryFactory   = $categoryFactory;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->menuItem = $item;
        $this->level    = $item->getLevel();
        $category       = $this->categoryFactory->create();
        $category->load((int)$item->getData('main_content_category_id'));
        if ($category->getId()) {
            $this->setCurrentCategory($category);
        }

        return $this;
    }

    /**
     * Get Level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;
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
     * @return boolean|integer
     */
    private function getCategoryId()
    {
        if ($item = $this->getItem()) {
            return (int)$item->getData('main_content_category_id');
        }

        return false;
    }

    /**
     * @return string
     */
    public function getContentHtml()
    {
        $html = $this->toHtml();
        $this->setCurrentCategory(null);

        return $html;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if ($this->getCategoryId()
            && $this->getCurrentCategory()
            && $this->getGroupCategory()
        ) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * Group Category
     *
     * @return \Magento\Catalog\Model\Category[]|null
     */
    public function groupCategory()
    {
        if ($this->groupCategory === null) {
            $collection = $this->collectionFactory->create();
            $collection->addIsActiveFilter();
            $collection->addUrlRewriteToResult();
            $collection->addNameToResult();
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($collection as $category) {
                $this->groupCategory[$category->getParentId()][] = $category;
            }
        }

        return $this->groupCategory;
    }

    /**
     * Set Current Category
     *
     * @param $category
     * @return $this
     */
    public function setCurrentCategory($category)
    {
        $this->currentCategory = $category;
        return $this;
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->currentCategory;
    }

    /**
     * Group children by config columns
     *
     * @return array|boolean
     */
    public function getGroupCategory()
    {
        $groupCategory = $this->groupCategory();
        if ($groupCategory && $this->getCurrentCategory()) {
            $catId = $this->getCurrentCategory()->getId();
            if (is_array($groupCategory) && !empty($groupCategory[$catId])) {
                $categories    = $groupCategory[$catId];
                $totals        = count($categories);
                $groupChildren = [];
                $childCols     = (int)$this->getItem()->getData('main_content_child_columns') ?: 1;

                //Set Child Column Sub-categories always = 1
                if ($this->getCategoryId() != $catId) {
                    $childCols = 1;
                }
                $col       = (int) floor($totals / $childCols);
                $remainder = $totals % $childCols;
                $temp      = $remainder > 0 ? $col + 1 : $col;
                $group     = 0;

                //Calculator Item By Group
                foreach ($categories as $category) {
                    if ($temp == 0) {
                        $remainder--;
                        $temp = $remainder > 0 ? $col + 1 : $col;
                        $group++;
                    }
                    $groupChildren[$group][] = $category;
                    $temp--;
                }
                return $groupChildren;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getAdditionalClass()
    {
        $childCols = (int)$this->getItem()->getData('main_content_child_columns') ?: 1;

        //Set Child Column Sub-categories always = 1
        if ($this->getCategoryId() != $this->getCurrentCategory()->getId()) {
            $childCols = 1;
        }
        $additionalClass = [
            'level' . $this->getLevel(),
            'bf-column-' . $childCols
        ];

        return implode(' ', $additionalClass);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG];
    }
}
