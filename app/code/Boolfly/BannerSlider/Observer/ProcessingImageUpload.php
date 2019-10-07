<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Banner Slider
 */
namespace Boolfly\BannerSlider\Observer;

use Boolfly\BannerSlider\Api\Data\BannerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Boolfly\BannerSlider\Model\ImageUploader;
use Boolfly\BannerSlider\Model\ImageField;

/**
 * Class ProcessingImageUpload
 *
 * @package Boolfly\BannerSlider\Observer
 */
class ProcessingImageUpload implements ObserverInterface
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * ProcessingImageUpload constructor.
     *
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $banner = $observer->getEvent()->getData('banner');
        if ($banner && $banner instanceof BannerInterface) {
            foreach (ImageField::getField() as $field) {
                $this->processFile($banner, $field);
            }
        }
    }

    /**
     * Process Field
     *
     * @param \Boolfly\BannerSlider\Model\Banner|BannerInterface $object
     * @param $key
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processFile(BannerInterface $object, $key)
    {
        $files = $object->getData($key);
        $object->unsetData($key);
        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                if (is_array($file) && empty($file['name'])) {
                    continue;
                }
                $name = $file['name'];
                // Upload New File
                if (isset($file['type'])) {
                    $this->imageUploader->moveFileFromTmp($name);
                } elseif (!empty($file['delete'])) {
                    $name = null;
                }
                $object->setData($key, $name);
            }
        }

        return $this;
    }
}
