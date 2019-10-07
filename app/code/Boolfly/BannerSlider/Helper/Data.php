<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Banner Slider
 */
namespace Boolfly\BannerSlider\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\Product;

/**
 * Class Data
 *
 * @package Boolfly\BannerSlider\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $directory;

    /**
     * @var AdapterFactory
     */
    private $imageFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * Data constructor.
     *
     * @param Context                     $context
     * @param StoreManagerInterface       $storeManager
     * @param AdapterFactory              $imageFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param EavConfig                   $eavConfig
     * @param Filesystem                  $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        AdapterFactory $imageFactory,
        CategoryRepositoryInterface $categoryRepository,
        EavConfig $eavConfig,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->filesystem         = $filesystem;
        $this->directory          = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory       = $imageFactory;
        $this->storeManager       = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->eavConfig          = $eavConfig;
    }

    /**
     * Get Resize Image
     *
     * @param $imageName
     * @param integer   $width
     * @param integer   $height
     * @return boolean|string
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getResizeImage($imageName, $width = 400, $height = 400)
    {
        $directoryRead = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $absolutePath  = $directoryRead->getAbsolutePath('catalog/category/') . $imageName;
        if (!$this->directory->isFile($absolutePath)
            || !$this->directory->isExist($absolutePath)
        ) {
            return false;
        }
        $cacheDirectory = 'bannerslider/banner/cache/' . $width . 'x' . $height. '/';
        $imageResized   = $directoryRead->getAbsolutePath($cacheDirectory) . $imageName;
        if (!$this->directory->isFile($imageResized)) {
            $imageResize = $this->imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(false);
            $imageResize->resize($width, $height);
            $imageResize->save($imageResized);
        }
        $resizeURL = $this->getBaseMediaUrl() . 'catalog/category/' . $imageName;

        return $resizeURL;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param $id
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryById($id)
    {
        return $this->categoryRepository->get($id);
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return boolean|string
     */
    public function getSubCategoriesImage($category)
    {
        try {
            $categoryObject = $this->getCategoryById($category->getId());
            return $this->getResizeCategoryImage($categoryObject->getHpCategoryImage(), 137, 250);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getBrandOptions()
    {
        $newOptions = [];
        try {
            $brandAttributeModel = $this->eavConfig->getAttribute(Product::ENTITY, 'brand');
            $options             = $brandAttributeModel->getSource()->getAllOptions();
            foreach ($options as $option) {
                $newOptions[$option['value']] = $option['label'];
            }
        } catch (LocalizedException $e) {
            return [];
        }

        return $newOptions;
    }
}
