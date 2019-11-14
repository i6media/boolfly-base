/**********************************************************************
 * banner-slider
 *
 * @copyright Copyright © Boolfly. All rights reserved.
 * @author    info@boolfly.com
 */
define([
    'jquery',
    'jquery/ui',
    'slick'
], function ($) {

    $.widget('boolfly.bannerSlider', {
        options: {
            fade: false,
            autoplay: false,
            autoplaySpeed: 5000
        },

        /**
         * Init Slick for Banner Slider
         *
         * @private
         */
        _create: function () {
            this.initSlick();
        },

        /**
         * Init Slick
         */
        initSlick: function () {
            var element = $(this.element);
            element.slick({
                arrows: true,
                dots: true,
                infinite: true,
                fade: this.options.fade,
                autoplay: this.options.autoplay,
                autoplaySpeed: this.options.autoplaySpeed,
                lazyLoad: 'ondemand',
                responsive:[
                    {
                        breakpoint: 1023,
                        settings:{
                            arrows: false,
                            dots: true
                        }
                },
                    {
                        breakpoint: 768,
                        settings:{
                            dots: false
                        }
                }
                ]
            });
            var parent = element.parent();
            $(this.element).on('lazyLoaded', function () {
                $('.slick-slider').get(0).slick.setPosition();
                element.removeClass('no-display');
                parent.find('[data-role=loader]').remove();
            })
        }
    });

    return $.boolfly.bannerSlider;
});