//
// This file is part of space theme for moodle
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
//
// space main JS file
//
// @package    theme_spacechild
// @copyright  Copyright © 2021 onwards Marcin Czaja Rosea Themes
//
// @license    Commercial

/* jshint ignore:start */
define(['jquery', 'core/log', 'core/aria'], function ($, log) {
    "use strict"; // ...jshint ;_; !!!

    log.debug('space AMD opt in functions');

    return {
        init: function () {
            $(document).ready(function ($) {
                var trigger = $(document.getElementById("darkModeBtn"));
                var preference = trigger.attr('data-preference');

                $('#darkModeBtn').click(function () {
                    if ($('body').hasClass('theme-dark')) {
                        $('body').removeClass('theme-dark');
                        $('html').removeClass('dark-mode');
                        trigger.attr('aria-checked', 'false');
                        M.util.set_user_preference(preference, 'false');
                    } else {
                        $('body').addClass('theme-dark');
                        $('html').addClass('dark-mode');
                        trigger.attr('aria-checked', 'true');
                        M.util.set_user_preference(preference, 'true');
                    }
                });

                var trigger2 = $(document.getElementById("myCoursesBtn"));
                var preference2 = trigger2.attr('data-preference');

                $('#myCoursesBtn').click(function () {
                    if ($('body').hasClass('mycourses-on')) {
                        $('body').removeClass('mycourses-on');
                        trigger2.attr('aria-checked', 'false');
                        M.util.set_user_preference(preference2, 'false');
                    } else {
                        $('body').addClass('mycourses-on');
                        trigger2.attr('aria-checked', 'true');
                        M.util.set_user_preference(preference2, 'true');
                    }
                });

                $('.btn-close-drawer--left').click(function () {
                    $('body').removeClass('drawer-courseindex--open');
                    $('body').removeClass('drawer-open-index--open');
                });

                if($( window ).width() < 991) {
                    $('.drawertoggle').click(function () {
                        $('body').removeClass('drawer-courseindex--open');
                        $('body').removeClass('drawer-open-index--open');
                    });
                }
            
                $('.btn-drawer--left').click(function () {
                    $('body').addClass('drawer-courseindex--open');
                    $('body').addClass('drawer-open-index--open');
                });

                $('#mobileNav, .rui-mobile-nav-btn-close').click(function () {
                    $('#topBar').toggleClass('opened');
                });

                $( ".editing .editmode-switch-form" ).on( "mouseover", function() {
                    $('body').addClass('rui-edit-areas');
                });

                $( ".editing .editmode-switch-form" ).on( "mouseout", function() {
                    $('body').removeClass('rui-edit-areas');
                });

                // Remove unnecessary string "..." from default moodle blocks
                // $('.block .footer').each(function () {
                //     var text = $(this).html();
                //     $(this).html(text.replace('...', ''));
                // });

                $('.rui-nav--admin .nav-link').each(function () {
                    var text = $(this).html();
                    $(this).html(text.replace('(', '<span class="mt-1 small d-block text-light">'));
                });
                $('.rui-nav--admin .nav-link').each(function () {
                    var text = $(this).html();
                    $(this).html(text.replace(')', '</span>'));
                });
            });

        }
    };
});
/* jshint ignore:end */
