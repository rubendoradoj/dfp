<?php
// This file is part of Moodle - http://moodle.org/
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

/**
 * A secure layout for the space theme.
 *
 * @package   theme_spacechild
 * @copyright 2022 - 2023 Marcin Czaja (https://rosea.io)
 * @license   Commercial https://themeforest.net/licenses
 */

defined('MOODLE_INTERNAL') || die();
user_preference_allow_ajax_update('darkmode-on', PARAM_ALPHA);
$extraclasses = [];

// Dark mode.
if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
    $draweropenright = (get_user_preferences('sidepre-open', 'true') == 'true');

    if (theme_spacechild_get_setting('darkmodetheme') == '1') {
        $darkmodeon = (get_user_preferences('darkmode-on', 'false') == 'true');
        if ($darkmodeon) {
            $extraclasses[] = 'theme-dark';
        }
    } else {
        $darkmodeon = false;
    }

    $mycourseson = (get_user_preferences('mycourses-on', 'false') == 'true');
    if ($mycourseson) {
        $extraclasses[] = 'mycourses-on';
    }

    $mycourseshiddenon = (get_user_preferences('mycourses-hidden-on', 'false') == 'true');
    if ($mycourseshiddenon) {
        $extraclasses[] = 'mycourses-hidden-on';
    }

    $mycoursesinprogresson = (get_user_preferences('mycourses-inprogress-on', 'false') == 'true');
    if ($mycoursesinprogresson) {
        $extraclasses[] = 'mycourses-inprogress-on';
    }
} else {
    $navdraweropen = false;
}

if (theme_spacechild_get_setting('darkmodefirst') == '1') {
    $extraclasses[] = 'theme-dark';
    $darkmodetheme = false;
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$bodyattributes = $OUTPUT->body_attributes();
$siteurl = $CFG->wwwroot;
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'draweropenright' => $draweropenright,
    'darkmodeon' => !empty($darkmodeon),
    'mycourseson' => !empty($mycourseson),
    'mycourseshiddenon' => !empty($mycourseshiddenon),
    'mycoursesinprogresson' => !empty($mycoursesinprogresson),
    'darkmodetheme' => !empty($darkmodetheme),
    'navdraweropen' => $navdraweropen,
    'siteurl' => $siteurl
];

// Load theme settings.
$themesettings = new \theme_spacechild\util\theme_settings();
$templatecontext = array_merge($templatecontext, $themesettings->global_settings());
$templatecontext = array_merge($templatecontext, $themesettings->footer_settings());

$PAGE->requires->js_call_amd('theme_spacechild/rui', 'init');
if (theme_spacechild_get_setting('backtotop') == '1') {
    $PAGE->requires->js_call_amd('theme_spacechild/backtotop', 'init');
}
echo $OUTPUT->render_from_template('theme_spacechild/secure', $templatecontext);
