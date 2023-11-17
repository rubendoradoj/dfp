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
// @copyright  Copyright © 2023 onwards Miguel Rojas
//
// @license    Commercial


define(['jquery', 'core/log', 'core/fragment'], function ($, log, Fragment) {
    "use strict";

    log.debug('space AMD medias');

    return {
        init: function (courseid) {
            var container = $(document.getElementById(`summary-media-${courseid}`));
            
            $.ajax({
                data: {"courseid" : courseid},
                type: "GET",
                dataType: "json",
                url: "classes/course_media/course_media.php",
            })
             .done(function( data, textStatus, jqXHR ) {
                 if ( console && console.log ) {
                    container.html(`Mi media: ${data.media} VS Media DFPOL: ${data.media_dfp}`);
                    if(data.media >= 5){
                        container.addClass("badge-success");
                    }else{
                        container.addClass("badge-danger");
                    }
                 }
             })
             .fail(function( jqXHR, textStatus, errorThrown ) {
                 if ( console && console.log ) {
                     console.log( "La solicitud a fallado: " +  textStatus);
                 }
            });
        }
    }
});