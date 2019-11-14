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
use Boolfly\BannerSlider\Helper\RedundantImageChecker;

/**
 * Class CheckingImageUploaded
 *
 * @package Boolfly\BannerSlider\Observer
 *
 * @event boolfly_banner_delete_commit_after
 */
class CheckingImageUploaded implements ObserverInterface
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var RedundantImageChecker
     */
    private $redundantImageChecker;

    /**
     * CheckingImageUploaded constructor.
     *
     * @param ImageUploader $imageUploader
     * @param RedundantImageChecker $redundantImageChecker
     */
    public function __construct(
        ImageUploader $imageUploader,
        RedundantImageChecker $redundantImageChecker
    ) {
        $this->imageUploader = $imageUploader;
        $this->redundantImageChecker = $redundantImageChecker;
    }

    /**
     * Dispatch event `boolfly_banner_delete_commit_after`
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $banner = $observer->getEvent()->getData('banner');
        if ($banner && $banner instanceof BannerInterface) {
            foreach (ImageField::getField() as $field) {
                $this->deleteFile($banner, $field);
            }
        }
    }

    /**
     * Delete File
     *
     * @param \Boolfly\BannerSlider\Model\Banner|BannerInterface $object
     * @param $key
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function deleteFile(BannerInterface $object, $key)
    {
        if ($image = $object->getData($key)) {
            if ($this->redundantImageChecker->checkImageUnused($image)) {
                $this->imageUploader->deleteImageFile($image);
            }
        }
    }
}
