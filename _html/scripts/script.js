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
         * Set X, Y, Z translate values for element with prefixes using jQuery css() method. The values can't be in percentage.
         * @param {Object} element - jQuery object for which we add CSS translate values
         * @param {Number} translateX - number of pixels for X axis of translate
         * @param {Number} translateY - number of pixels for Y axis of translate
         * @param {Number} translateZ - number of pixels for Z axis of translate
         */
        function setTranslate(element, translateX, translateY, translateZ) {
            element.css({
                "-webkit-transform": "translate3d(" + translateX + "px, " + translateY + "px, " + translateZ + "px)",
                "-ms-transform": "translate3d(" + translateX + "px, " + translateY + "px, " + translateZ + "px)",
                "-moz-transform": "translate3d(" + translateX + "px, " + translateY + "px, " + translateZ + "px)",
                "-o-transform": "translate3d(" + translateX + "px, " + translateY + "px, " + translateZ + "px)",
                "transform": "translate3d(" + translateX + "px, " + translateY + "px, " + translateZ + "px)"
            });
        }
        
        /** 
         * Set CSS transition for CSS translate property for element with prefixes using jQuery css() method.
         * @param {Object} element - jQuery object for which we add CSS translate transition
         * @param {Number} transitionDuration - number of seconds for the transition duration property 
         */
        function setTranslateTransition(element, transitionDuration) {
            element.css({
                "-webkit-transition": "transform " + transitionDuration + "s ease-in-out",
                "-moz-transition": "transform " + transitionDuration + "s ease-in-out",
                "-o-transition": "transform " + transitionDuration + "s ease-in-out",
                "transition": "transform " + transitionDuration + "s ease-in-out"
            });
        }

        /**
         * Add scroll event handler.
         * @param {Function} handler - handler function to add to scroll on window
         */
        function attachScrollHandler(handler) {
            $(window).on('scroll', handler);
            console.log('attached');
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
            if (typeof jQuery === "function" && el instanceof jQuery) {
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

        function recalculateElementPosition(distancePerScroll) {
            console.log('recalculate');

            console.log(distancePerScroll);
        }

        /**
         * Initialize module for the element.
         */
        function init(responsiveBreakpoint, distancePerScroll, animationStopPoint) {
            //animateElement(distancePerScroll);
            var handler = recalculateElementPosition(distancePerScroll);
            
            if (typeof jQuery !== "function" && el instanceof jQuery) {
                if (element.length) {
                    if ($(window).width() > responsiveBreakpoint) {
                        attachScrollHandler(handler);
                    }

                    var oldWidth = $(window).width();
                    
                    /* Handle resizing */
                    $(window).on('resize', throttle(function() {
                        console.log();
                        var newWidth = $(window).width();
                        if ($(window).width() <= responsiveBreakpoint && oldWidth > responsiveBreakpoint) {
                            removeScrollHandler(handler);
                        } else if ($(window).width() > responsiveBreakpoint && oldWidth <= responsiveBreakpoint) {
                            attachScrollHandler(handler);
                        }
        
                        oldWidth = newWidth;
                    }, 100));
                }
            } else {
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
