<?php
/************************************************************
 * *
 *  * Copyright © 2019 Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Banner Slider
 */
namespace Boolfly\BannerSlider\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Boolfly\BannerSlider\Model\ImageUploader;

/**
 * Class Thumbnail
 *
 * @package Boolfly\BannerSlider\Ui\Component\Listing\Columns
 */
class Thumbnail extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * Thumbnail constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param ImageUploader      $imageUploader
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ImageUploader $imageUploader,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (empty($item[$fieldName])) {
                    continue;
                }
                $imageName                      = $item[$fieldName];
                $item[$fieldName . '_src']      = $this->imageUploader->getImageUrl($imageName);
                $item[$fieldName . '_alt']      = $item['image_alt'];
                $item[$fieldName . '_link']     = $this->urlBuilder->getUrl(
                    'bannerslider/banner/edit',
                    ['id' => $item['banner_id'], 'store' => $this->context->getRequestParam('store')]
                );
                $item[$fieldName . '_orig_src'] = $this->imageUploader->getImageUrl($imageName);
            }
        }

        return $dataSource;
    }
}
