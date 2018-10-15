/**
 * @file
 * DCT image slider behaviors.
 */

(function($, Drupal) {

    'use strict';

    Drupal.behaviors.dctImageSlider = {
        attach: function (context, settings) {
            var dCampTrans = {
                /* Responsive breakpoints. These are min-widths for the respective breakpoints. Min-width for mobile is 0. */
                TABLET_WIDTH: 768,
                MEDIUM_DESKTOP_WIDTH: 1024,

                /* Force page to load at the top. */
                loadPageAtTop: function () {
                    $(document).scrollTop(0);
                    $(window).on('beforeunload', function () {
                        $(window).scrollTop(0);
                    });
                },

                locationCreateSlider: function () {
                    var $slider = $('#location-slider');
                    var sliderOptions = {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        responsive: [
                            {
                                breakpoint: this.MEDIUM_DESKTOP_WIDTH,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1
                                }
                            },
                            {
                                breakpoint: this.TABLET_WIDTH,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    };

                    if ($slider.length) {
                        $slider.slick(sliderOptions);
                    }
                }
            };
            /**
             * Execute methods after DOM has loaded.
             */
            $(document).ready(function() {
                dCampTrans.locationCreateSlider();
            });
        }
    };

})(jQuery, Drupal);
