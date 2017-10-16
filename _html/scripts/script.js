(function($) {
    /**
     * Module for animating an element based on scrolling the page. More specifically, 
     * the element will change its position when page is scrolled up and down, until a given part of the page is reached.
     * When creating different instances, which are displayed next to each other or on top of each other, changing their 
     * position at different speeds when scrolling the page will create a so-called 'parallax effect'.
     * @param {Object} element - jQuery object for which we initialize the module
     */
    function parallaxModule(element) {
        /**
         * Create universal requestAnimationFrame() function that works in all browsers. setTimeout() as last fallback.
         */
        window.requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame || function(f) {return setTimeout(f, 1000/60);};
        
        /**
         * Create universal cancelAnimationFrame() function that works in all browsers. clearTimeout() as last fallback. 
         */
        window.cancelAnimationFrame = window.cancelAnimationFrame || window.mozCancelAnimationFrame || function(requestID) {clearTimeout(requestID);};

        /* Store latest window scrollY value. By default at page load it will be 0.*/
        var latestScrollY = 0;
        
        /* With this flag we make sure that we don't request another animation frame if one is already requested. */
        var readyToAnimate = false;

        /* Miliseconds to throttle resize events. */
        var throttleTime = 100;

        /**
         * Throttle function for limiting hom many times we call event heavy functions.
         * @param {Function} callback - function to call
         * @param {Number} limit - number of miliseconds
         */
        function throttle(callback, limit) {
            var wait = false;
            return function() {
                if (!wait) {
                    callback.call();
                    wait = true;
                    setTimeout(function() {
                        wait = false;
                    }, limit);
                }
            };
        }

        /**
         * Check if object is a jQuery instance and that it is not empty.
         * @param {Qbject} element - object to check.
         */
        function isJQuery(element) {
            return (typeof jQuery === 'function' && element instanceof jQuery && element.length);
        }

        /** 
         * Change element's top and bottom CSS properties using jQuery css() method. Only works for values in pixels.
         * @param {Object} element - jQuery object for which we change top and bottom
         * @param {Number} top - number of pixels to change top
         * @param {Number} bottom - number of pixels to change bottom
         */
        function setTopAndBottom(element, top, bottom) {
            element.css({
                'top': top + 'px',
                'bottom': bottom + 'px'
            });
        }

        /**
         * Add scroll event handler.
         * @param {Function} handler - handler function to add to scroll on window
         */
        function attachScrollHandler(handler) {
            $(window).on('scroll', handler);
        }

        /**
         * Remove scroll event handler.
         * @param {Function} handler - handler function to remove from scroll on window
         */
        function removeScrollHandler(handler) {
            $(window).off('scroll', handler);
        }

        /**
         * Remove handler on window resize event when a certain breakpoint is reached or add it back when resizing in the opposite direction.
         * @param {Number} responsiveBreakpoint - Breakpoint for which we remove or add back handler.
         * @param {Function} handler - Handler to remove/add
         */
        function removeHandlerOnResize(responsiveBreakpoint, handler) {
            var oldWidth = $(window).outerWidth();
            
            /* Remove scroll event listener if window is resized to under the specified breakpoint. 
            Re-add scroll event listener when resized to the specified breakpoint. */
            $(window).on('resize', throttle(function() {
                var newWidth = $(window).outerWidth();

                if ($(window).outerWidth() < responsiveBreakpoint && oldWidth >= responsiveBreakpoint) {
                    removeScrollHandler(handler);
                    setTopAndBottom(element, 0, 0);
                } 
                else if ($(window).outerWidth() >= responsiveBreakpoint && oldWidth < responsiveBreakpoint) {
                    /* Reset scroll to zero so when we start again the calculations are correct. */
                    $(window).scrollTop(0);
                    latestScrollY = 0;

                    if (window.pageYOffset === 0) {
                        attachScrollHandler(handler);
                    }
                }

                oldWidth = newWidth;
            }, throttleTime));
        }

        /**
         * Animation callback. It can be passed to requestAnimationFrame.
         * Function changes top and bottom positions of global element with the specified distance at scroll event, based on scroll direction.
         * @param {Number} distance - distance in pixels that element will travel.
         * @param {Number} oldScrollY - old window scroll position.
         * @param {Number} latestScrollY - current window scroll position.
         */
        function recalculateElementPosition(speed, oldScrollY, latestScrollY) {
            var currentTop = parseFloat(element.css('top'));
            var currentBottom = parseFloat(element.css('bottom'));

            /* Distance in pixels that was travelled in the current scroll. */
            var scrollDistance = latestScrollY - oldScrollY;

            /* Distance in pixels that element has to move, either up or down. */
            var elementOffset = scrollDistance * speed;

            var newTop = currentTop - elementOffset;
            var newBottom = currentBottom + elementOffset;

            setTopAndBottom(element, newTop, newBottom);

            /* This allows further requestAnimationFrames to be called */
            readyToAnimate = false;
        }

        /**
         * Initialize module for the element.
         * @param {Number} responsiveBreakpoint - minimum breakpoint for which module is initialized.
         * @param {Number} speedPerScroll - multiplier for changing the distance element travels on one scroll event. Different speeds for elements gives the illusion of parallax.  
         */
        function init(responsiveBreakpoint, speedPerScroll) {
            /* Call requestAnimationFrame if it's not called already. */
            function startAnimation() {
                var oldScrollY = latestScrollY;
                latestScrollY = window.pageYOffset;

                if (!readyToAnimate) {
                    /* Call native browser requestAnimationFrame function which tells the browser we want to perform an 
                    animation before the next repaint. */
                    requestAnimationFrame(function () {
                        recalculateElementPosition(speedPerScroll, oldScrollY, latestScrollY);
                    });
                }
            }

            /* Callback we'll pass to our scroll event. */
            var handlerOnScroll = function() {
                startAnimation();
            };

            if (isJQuery(element)) {
                if ($(window).outerWidth() > responsiveBreakpoint) {
                    attachScrollHandler(handlerOnScroll);
                }

                removeHandlerOnResize(responsiveBreakpoint, handlerOnScroll);
            }
            else {
                return;
            }
        }

        /**
         * Public API.
         */
        var publicParallax = {
            init: init
        };

        return publicParallax;
    }

    /**
     * Global object
     */
    var dCampTrans = {
        /* Responsive breakpoints. These are min-widths for the respective breakpoints. Min-width for mobile is 0. */
        TABLET_WIDTH: 768,
        DESKTOP_WIDTH: 1024,

        /* Force page to load at the top. */
        loadPageAtTop: function() {
            $(document).scrollTop(0);
            $(window).on('beforeunload', function() {
                $(window).scrollTop(0);
            });
        },

        /* Initialize parallax items on homepage */
        homepageCreateParallax: function() {
            var $parallaxItems = $('.homepage-parallax--item').not('.item-0');
            var prxLength = $parallaxItems.length;

            if (prxLength) {
                var speedsPerScroll = [0.4, 0.6, 0.7, 1, 1.8];
        
                for (var i = 0; i < prxLength; i++) {
                    var parallaxItem = new parallaxModule($parallaxItems.eq(i));
                    parallaxItem.init(this.DESKTOP_WIDTH, speedsPerScroll[i]);
                }
            }
        },

        locationCreateSlider: function() {
            var $slider = $('#location-slider');
            var sliderOptions = {
                slidesToShow: 3,
                slidesToScroll: 1,
                responsive: [
                    {
                        breakpoint: this.DESKTOP_WIDTH,
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
        },

        locationMap: function() {
            var $locationMap = $('.location-map');

            if ($locationMap.length) {
                var center = {lat: 46.7764, lng: 23.6036};
    
                var map = new google.maps.Map(document.getElementById('location-map'), {
                    zoom: 17,
                    center: center
                });

                var marker = new google.maps.Marker({
                    position: center,
                    map: map
                });
            }
        },

        sponsorsMatchHeight: function() {
            var sponsorCategory = $('.sponsors-categories--category');

            sponsorCategory.matchHeight();
        }
    };

    /**
     * Execute methods after DOM has loaded.
     */
    $(document).ready(function() {
        dCampTrans.loadPageAtTop();
        dCampTrans.homepageCreateParallax();
        dCampTrans.locationCreateSlider();
        dCampTrans.locationMap();
        dCampTrans.sponsorsMatchHeight();
    });
})(jQuery);
