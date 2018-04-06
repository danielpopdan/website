/**
 * @file
 * DrupalCampTransylvania matchHeight behaviors.
 */

(function ($, Drupal) {

    'use strict';

    var menuButton = $('.burger-button');
    var menuButtonLayers = $('.btn-layers');
    var modalBurgerMenu = $('.burger-menu');
    var burgermenuActionLink = $('.menu-item--expanded > a');
    var sublinks = $('.sublinks');

    menuButton.on('click', function () {
        menuButtonLayers.toggleClass('btn-layers-fade');
        modalBurgerMenu.fadeToggle(0);
        modalBurgerMenu.toggleClass('opened');


    });

    burgermenuActionLink.one('click', function () {
        $(this).toggleClass('is-active');
        $(this).siblings(sublinks).fadeToggle();

        return false;
    });

})(jQuery, Drupal);
