/**
 * @file
 * DrupalCampTransylvania matchHeight behaviors.
 */

(function ($, Drupal) {

    'use strict';

    /**
     *
     * @type {Drupal~behavior}
     */
    Drupal.behaviors.dctMatchHeightFunctionality = {
        attach: function () {
            var menuButton = $('.burger-button');
            var menuButtonLayers = $('.btn-layers');
            var modalBurgerMenu = $('.burger-menu');
            var burgermenuActionLink = $('.action-link');
            var sublinks = $('.sublinks');

            menuButton.on('click', function () {
                menuButtonLayers.toggleClass('btn-layers-fade');
                modalBurgerMenu.fadeToggle();

            });

            burgermenuActionLink.on('click', function () {
                $(this).toggleClass('active-burger-link');
                $(this).siblings(sublinks).fadeToggle();
            });
        }
    };

})(jQuery, Drupal);
