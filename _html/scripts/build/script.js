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
        MEDIUM_DESKTOP_WIDTH: 1024,

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
                var speedsPerScroll = [0.15, 0.3, 0.5, 0.75, 1.2];
        
                for (var i = 0; i < prxLength; i++) {
                    var parallaxItem = new parallaxModule($parallaxItems.eq(i));
                    parallaxItem.init(this.MEDIUM_DESKTOP_WIDTH, speedsPerScroll[i]);
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
        },

        burgerMenu: function() {
            var menuButton = $('.burger-button');
            var menuButtonLayers = $('.btn-layers');
            var modalBurgerMenu = $('.burger-menu');
            var burgermenuActionLink = $('.menu-item--expanded > a');
            var sublinks = $('.sublinks');
        
            menuButton.on('click', function() {
                menuButtonLayers.toggleClass('btn-layers-fade');
                modalBurgerMenu.fadeToggle();
                
            });
            
            burgermenuActionLink.on('click', function() {
                $(this).toggleClass('is-active');
                $(this).siblings(sublinks).fadeToggle();

                return false;
            });
        },
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
        dCampTrans.burgerMenu();
    });
})(jQuery);

/* ========================================================================
 * Bootstrap: transition.js v3.3.7
 * http://getbootstrap.com/javascript/#transitions
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
    'use strict';
  
    // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
    // ============================================================
  
    function transitionEnd() {
      var el = document.createElement('bootstrap')
  
      var transEndEventNames = {
        WebkitTransition : 'webkitTransitionEnd',
        MozTransition    : 'transitionend',
        OTransition      : 'oTransitionEnd otransitionend',
        transition       : 'transitionend'
      }
  
      for (var name in transEndEventNames) {
        if (el.style[name] !== undefined) {
          return { end: transEndEventNames[name] }
        }
      }
  
      return false // explicit for ie8 (  ._.)
    }
  
    // http://blog.alexmaccaw.com/css-transitions
    $.fn.emulateTransitionEnd = function (duration) {
      var called = false
      var $el = this
      $(this).one('bsTransitionEnd', function () { called = true })
      var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
      setTimeout(callback, duration)
      return this
    }
  
    $(function () {
      $.support.transition = transitionEnd()
  
      if (!$.support.transition) return
  
      $.event.special.bsTransitionEnd = {
        bindType: $.support.transition.end,
        delegateType: $.support.transition.end,
        handle: function (e) {
          if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
        }
      }
    })
  
  }(jQuery);

  +function ($) { "use strict";
  
    /**
     * The zoom service
     */
    function ZoomService () {
      this._activeZoom            =
      this._initialScrollPosition =
      this._initialTouchPosition  =
      this._touchMoveListener     = null
  
      this._$document = $(document)
      this._$window   = $(window)
      this._$body     = $(document.body)
  
      this._boundClick = $.proxy(this._clickHandler, this)
    }
  
    ZoomService.prototype.listen = function () {
      this._$body.on('click', '[data-action="zoom"]', $.proxy(this._zoom, this))
    }
  
    ZoomService.prototype._zoom = function (e) {
      var target = e.target
  
      if (!target || target.tagName != 'IMG') return
  
      if (this._$body.hasClass('zoom-overlay-open')) return
  
      if (e.metaKey || e.ctrlKey) {
        return window.open((e.target.getAttribute('data-original') || e.target.src), '_blank')
      }
  
      if (target.width >= ($(window).width() - Zoom.OFFSET)) return
  
      this._activeZoomClose(true)
  
      this._activeZoom = new Zoom(target)
      this._activeZoom.zoomImage()
  
      // todo(fat): probably worth throttling this
      this._$window.on('scroll.zoom', $.proxy(this._scrollHandler, this))
  
      this._$document.on('keyup.zoom', $.proxy(this._keyHandler, this))
      this._$document.on('touchstart.zoom', $.proxy(this._touchStart, this))
  
      // we use a capturing phase here to prevent unintended js events
      // sadly no useCapture in jquery api (http://bugs.jquery.com/ticket/14953)
      if (document.addEventListener) {
        document.addEventListener('click', this._boundClick, true)
      } else {
        document.attachEvent('onclick', this._boundClick, true)
      }
  
      if ('bubbles' in e) {
        if (e.bubbles) e.stopPropagation()
      } else {
        // Internet Explorer before version 9
        e.cancelBubble = true
      }
    }
  
    ZoomService.prototype._activeZoomClose = function (forceDispose) {
      if (!this._activeZoom) return
  
      if (forceDispose) {
        this._activeZoom.dispose()
      } else {
        this._activeZoom.close()
      }
  
      this._$window.off('.zoom')
      this._$document.off('.zoom')
  
      document.removeEventListener('click', this._boundClick, true)
  
      this._activeZoom = null
    }
  
    ZoomService.prototype._scrollHandler = function (e) {
      if (this._initialScrollPosition === null) this._initialScrollPosition = $(window).scrollTop()
      var deltaY = this._initialScrollPosition - $(window).scrollTop()
      if (Math.abs(deltaY) >= 40) this._activeZoomClose()
    }
  
    ZoomService.prototype._keyHandler = function (e) {
      if (e.keyCode == 27) this._activeZoomClose()
    }
  
    ZoomService.prototype._clickHandler = function (e) {
      if (e.preventDefault) e.preventDefault()
      else event.returnValue = false
  
      if ('bubbles' in e) {
        if (e.bubbles) e.stopPropagation()
      } else {
        // Internet Explorer before version 9
        e.cancelBubble = true
      }
  
      this._activeZoomClose()
    }
  
    ZoomService.prototype._touchStart = function (e) {
      this._initialTouchPosition = e.touches[0].pageY
      $(e.target).on('touchmove.zoom', $.proxy(this._touchMove, this))
    }
  
    ZoomService.prototype._touchMove = function (e) {
      if (Math.abs(e.touches[0].pageY - this._initialTouchPosition) > 10) {
        this._activeZoomClose()
        $(e.target).off('touchmove.zoom')
      }
    }
  
  
    /**
     * The zoom object
     */
    function Zoom (img) {
      this._fullHeight      =
      this._fullWidth       =
      this._overlay         =
      this._targetImageWrap = null
  
      this._targetImage = img
  
      this._$body = $(document.body)
    }
  
    Zoom.OFFSET = 80
    Zoom._MAX_WIDTH = 2560
    Zoom._MAX_HEIGHT = 4096
  
    Zoom.prototype.zoomImage = function () {
      var img = document.createElement('img')
      img.onload = $.proxy(function () {
        this._fullHeight = Number(img.height)
        this._fullWidth = Number(img.width)
        this._zoomOriginal()
      }, this)
      img.src = this._targetImage.src
    }
  
    Zoom.prototype._zoomOriginal = function () {
      this._targetImageWrap           = document.createElement('div')
      this._targetImageWrap.className = 'zoom-img-wrap'
  
      this._targetImage.parentNode.insertBefore(this._targetImageWrap, this._targetImage)
      this._targetImageWrap.appendChild(this._targetImage)
  
      $(this._targetImage)
        .addClass('zoom-img')
        .attr('data-action', 'zoom-out')
  
      this._overlay           = document.createElement('div')
      this._overlay.className = 'zoom-overlay'
  
      document.body.appendChild(this._overlay)
  
      this._calculateZoom()
      this._triggerAnimation()
    }
  
    Zoom.prototype._calculateZoom = function () {
      this._targetImage.offsetWidth // repaint before animating
  
      var originalFullImageWidth  = this._fullWidth
      var originalFullImageHeight = this._fullHeight
  
      var scrollTop = $(window).scrollTop()
  
      var maxScaleFactor = originalFullImageWidth / this._targetImage.width
  
      var viewportHeight = ($(window).height() - Zoom.OFFSET)
      var viewportWidth  = ($(window).width() - Zoom.OFFSET)
  
      var imageAspectRatio    = originalFullImageWidth / originalFullImageHeight
      var viewportAspectRatio = viewportWidth / viewportHeight
  
      if (originalFullImageWidth < viewportWidth && originalFullImageHeight < viewportHeight) {
        this._imgScaleFactor = maxScaleFactor
  
      } else if (imageAspectRatio < viewportAspectRatio) {
        this._imgScaleFactor = (viewportHeight / originalFullImageHeight) * maxScaleFactor
  
      } else {
        this._imgScaleFactor = (viewportWidth / originalFullImageWidth) * maxScaleFactor
      }
    }
  
    Zoom.prototype._triggerAnimation = function () {
      this._targetImage.offsetWidth // repaint before animating
  
      var imageOffset = $(this._targetImage).offset()
      var scrollTop   = $(window).scrollTop()
  
      var viewportY = scrollTop + ($(window).height() / 2)
      var viewportX = ($(window).width() / 2)
  
      var imageCenterY = imageOffset.top + (this._targetImage.height / 2)
      var imageCenterX = imageOffset.left + (this._targetImage.width / 2)
  
      this._translateY = viewportY - imageCenterY
      this._translateX = viewportX - imageCenterX
  
      var targetTransform = 'scale(' + this._imgScaleFactor + ')'
      var imageWrapTransform = 'translate(' + this._translateX + 'px, ' + this._translateY + 'px)'
  
      if ($.support.transition) {
        imageWrapTransform += ' translateZ(0)'
      }
  
      $(this._targetImage)
        .css({
          '-webkit-transform': targetTransform,
              '-ms-transform': targetTransform,
                  'transform': targetTransform
        })
  
      $(this._targetImageWrap)
        .css({
          '-webkit-transform': imageWrapTransform,
              '-ms-transform': imageWrapTransform,
                  'transform': imageWrapTransform
        })
  
      this._$body.addClass('zoom-overlay-open')
    }
  
    Zoom.prototype.close = function () {
      this._$body
        .removeClass('zoom-overlay-open')
        .addClass('zoom-overlay-transitioning')
  
      // we use setStyle here so that the correct vender prefix for transform is used
      $(this._targetImage)
        .css({
          '-webkit-transform': '',
              '-ms-transform': '',
                  'transform': ''
        })
  
      $(this._targetImageWrap)
        .css({
          '-webkit-transform': '',
              '-ms-transform': '',
                  'transform': ''
        })
  
      if (!$.support.transition) {
        return this.dispose()
      }
  
      $(this._targetImage)
        .one($.support.transition.end, $.proxy(this.dispose, this))
        .emulateTransitionEnd(300)
    }
  
    Zoom.prototype.dispose = function () {
      if (this._targetImageWrap && this._targetImageWrap.parentNode) {
        $(this._targetImage)
          .removeClass('zoom-img')
          .attr('data-action', 'zoom')
  
        this._targetImageWrap.parentNode.replaceChild(this._targetImage, this._targetImageWrap)
        this._overlay.parentNode.removeChild(this._overlay)
  
        this._$body.removeClass('zoom-overlay-transitioning')
      }
    }
  
    // wait for dom ready (incase script included before body)
    $(function () {
      new ZoomService().listen()
    })
  
  }(jQuery)
