(function($) {
    /**
     * Responsive breakpoints. These are min-widths for the respective breakpoints. Min-width for mobile is 0;
     */
    var TABLET_WIDTH = 768;
    var DESKTOP_WIDTH = 1024;
    
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

        /**
         * Throttle function for limiting hom many times we call event heavy functions.\
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
         * Check if element is in viewport.
         * @param {Object} element 
         * @return {Boolean}
         */
        function isElementInViewport(element) {
            if (typeof jQuery === 'function' && el instanceof jQuery) {
                el = el[0];
            }
        
            var rect     = el.getBoundingClientRect(),
                vWidth   = window.innerWidth || doc.documentElement.clientWidth,
                vHeight  = window.innerHeight || doc.documentElement.clientHeight,
                efp      = function (x, y) { return document.elementFromPoint(x, y); };     
        
            /* Return false if it's not in the viewport. */
            if (rect.right < 0 || rect.bottom < 0 || rect.left > vWidth || rect.top > vHeight) {
                return false;
            }

            /* Return true if any of its four corners are visible. */
            return (el.contains(efp(rect.left,  rect.top)) ||  el.contains(efp(rect.right, rect.top)) ||  el.contains(efp(rect.right, rect.bottom)) ||  el.contains(efp(rect.left,  rect.bottom)));
        }

        /**
         * Execute callback when element has entered viewport.
         * @param {Object} element - jQuery object which we check if it has entered the viewport.
         * @param {Function} callback
         */
        function onVisibilityChange(element, callback) {
            var oldVisible = false;
            
            return function() {
                var visible = isElementInViewport(element);

                if (visible !== oldVisible) {
                    oldVisible = visible;

                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            };
        }
        
        /**
         * Animation callback. It can be passed to requestAnimationFrame.
         * Function changes top and bottom positions of global element with the specified distance at scroll event, based on scroll direction.
         * @param {Number} distance - distance in pixels that element will travel.
         * @param {Number} oldScrollY - old window scroll position.
         * @param {Number} latestScrollY - current window scroll position.
         */
        function recalculateElementPosition(distance, oldScrollY, latestScrollY) {
            var currentTop = parseInt(element.css('top'));
            var currentBottom = parseInt(element.css('bottom'));

            if (oldScrollY < latestScrollY) {
                setTopAndBottom(element, currentTop - distance, currentBottom + distance);
            }
            else {
                setTopAndBottom(element, currentTop + distance, currentBottom - distance);
            }

            /* This allows further requestAnimationFrames to be called */
            readyToAnimate = false;
        }

        /**
         * Initialize module for the element.
         */
        function init(responsiveBreakpoint, distancePerScroll, animationStopPoint) {
            /* Call requestAnimationFrame if it's not called already. */
            function startAnimation() {
                var oldScrollY = latestScrollY;
                latestScrollY = window.scrollY;

                if (!readyToAnimate) {
                    requestAnimationFrame(function () {
                        recalculateElementPosition(distancePerScroll, oldScrollY, latestScrollY);
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

                var oldWidth = $(window).outerWidth();
                
                /* Remove scroll event listener if window is resized to under the specified breakpoint. 
                Re-add scroll event listener when resized to the specified breakpoint. */
                $(window).on('resize', throttle(function() {
                    var newWidth = $(window).outerWidth();

                    if ($(window).outerWidth() < responsiveBreakpoint && oldWidth >= responsiveBreakpoint) {
                        removeScrollHandler(handler);
                    } 
                    else if ($(window).outerWidth() >= responsiveBreakpoint && oldWidth < responsiveBreakpoint) {
                        attachScrollHandler(handler);
                    }
    
                    oldWidth = newWidth;
                }, 100));
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

    $(document).ready(function() {
        /* Force page to load at the top. */
        $(this).scrollTop(0);
        $(window).on('beforeunload', function() {
            $(window).scrollTop(0);
        });

        var $parallaxItems = $('.homepage-parallax--item');
        var prxLength = $parallaxItems.length;
        var distancePerScroll = 1;
        var $stopPoint = $('.footer');

        for (var i = 0; i < prxLength; i++) {
            var parallaxItem = parallaxModule($parallaxItems.eq(i));

            parallaxItem.init(DESKTOP_WIDTH, distancePerScroll, $stopPoint);
        }
    });
})(jQuery);
