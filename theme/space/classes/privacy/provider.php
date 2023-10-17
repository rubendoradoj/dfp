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
 * Privacy Subsystem implementation for theme_space.
 *
 * @package    theme_space
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_space\privacy;

use \core_privacy\local\metadata\collection;

/*
 * The space theme stores a user preference data.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,
    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider {

    /** The user preference for the Dark Mode UI. */
    const DARK_MODE_ON = 'darkmode-on';
    /** The user preference for the My Courses Box. */
    const MY_COURSES_ON = 'mycourses-on';
    const MY_COURSES_HIDDEN_ON = 'mycourses-hidden-on';
    const MY_COURSES_INPROGRESS_ON = 'mycourses-inprogress-on';

    /** The user preference for the sidebar. */
    const RIGHT_SIDEBAR_OPEN = 'sidepre-open';
    /** The user preference for the navigation drawer. */
    const DRAWER_OPEN_NAV = 'drawer-open-nav';
    /** The user preferences for the course index. */
    const DRAWER_OPEN_INDEX = 'drawer-open-index';
    /** The user preferences for the blocks drawer. */
    const DRAWER_OPEN_BLOCK = 'drawer-open-block';

    /**
     * Returns meta data about this system.
     *
     * @param  collection $items The initialised item collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items): collection {
        $items->add_user_preference(self::DARK_MODE_ON, 'privacy:metadata:preference:darkmodeon');
        $items->add_user_preference(self::MY_COURSES_ON, 'privacy:metadata:preference:darkmodeon');
        $items->add_user_preference(self::RIGHT_SIDEBAR_OPEN, 'privacy:metadata:preference:sidebaropen');
        $items->add_user_preference(self::DRAWER_OPEN_NAV, 'privacy:metadata:preference:draweropennav');
        $items->add_user_preference(self::DRAWER_OPEN_INDEX, 'privacy:metadata:preference:draweropenindex');
        $items->add_user_preference(self::DRAWER_OPEN_BLOCK, 'privacy:metadata:preference:draweropenblock');
        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $darkmodepref = get_user_preferences(self::DARK_MODE_ON, null, $userid);
        if (isset($darkmodepref)) {
            $preferencestring = get_string('privacy:darkmodeoff', 'theme_space');
            if ($darkmodepref !== null) {
                $preferencestring = get_string('privacy:darkmodeon', 'theme_space');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_space',
                self::DARK_MODE_ON,
                $darkmodepref,
                $preferencestring
            );
        }

        $mycoursespref = get_user_preferences(self::MY_COURSES_ON, null, $userid);
        if (isset($mycoursespref)) {
            $preferencestring = get_string('privacy:darkmodeoff', 'theme_space');
            if ($mycoursespref !== null) {
                $preferencestring = get_string('privacy:darkmodeon', 'theme_space');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_space',
                self::MY_COURSES_ON,
                $mycoursespref,
                $preferencestring
            );
        }

        $mycourseshiddenpref = get_user_preferences(self::MY_COURSES_HIDDEN_ON, null, $userid);
        if (isset($mycourseshiddenpref)) {
            $preferencestring = get_string('privacy:darkmodeoff', 'theme_space');
            if ($mycourseshiddenpref !== null) {
                $preferencestring = get_string('privacy:darkmodeon', 'theme_space');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_space',
                self::MY_COURSES_HIDDEN_ON,
                $mycourseshiddenpref,
                $preferencestring
            );
        }

        $mycoursesinprogresspref = get_user_preferences(self::MY_COURSES_INPROGRESS_ON, null, $userid);
        if (isset($mycoursesinprogresspref)) {
            $preferencestring = get_string('privacy:darkmodeoff', 'theme_space');
            if ($mycoursesinprogresspref !== null) {
                $preferencestring = get_string('privacy:darkmodeon', 'theme_space');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_space',
                self::MY_COURSES_INPROGRESS_ON,
                $mycoursesinprogresspref,
                $preferencestring
            );
        }

        $draweropennavpref = get_user_preferences(self::DRAWER_OPEN_NAV, null, $userid);
        if (isset($draweropennavpref)) {
            $preferencestring = get_string('privacy:drawernavclosed', 'theme_space');
            if ($draweropennavpref == 'true') {
                $preferencestring = get_string('privacy:drawernavopen', 'theme_space');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_space',
                self::DRAWER_OPEN_NAV,
                $draweropennavpref,
                $preferencestring
            );
        }

        $draweropenindexpref = get_user_preferences(self::DRAWER_OPEN_INDEX, null, $userid);

        if (isset($draweropenindexpref)) {
            $preferencestring = get_string('privacy:drawerindexclosed', 'theme_space');
            if ($draweropenindexpref == 1) {
                $preferencestring = get_string('privacy:drawerindexopen', 'theme_space');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_space',
                self::DRAWER_OPEN_INDEX,
                $draweropenindexpref,
                $preferencestring
            );
        }

        $draweropenblockpref = get_user_preferences(self::DRAWER_OPEN_BLOCK, null, $userid);

        if (isset($draweropenblockpref)) {
            $preferencestring = get_string('privacy:drawerblockclosed', 'theme_space');
            if ($draweropenblockpref == 1) {
                $preferencestring = get_string('privacy:drawerblockopen', 'theme_space');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_space',
                self::DRAWER_OPEN_BLOCK,
                $draweropenblockpref,
                $preferencestring
            );
        }

        $rightdraweropennpref = get_user_preferences(self::RIGHT_SIDEBAR_OPEN, null, $userid);
        if (isset($draweropennavpref)) {
            $preferencestring = get_string('privacy:rightdrawerclosed', 'theme_space');
            if ($rightdraweropennpref !== null) {
                $preferencestring = get_string('privacy:rightdraweropen', 'theme_space');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_space',
                self::DRAWER_OPEN_NAV,
                $draweropennavpref,
                $preferencestring
            );
        }
    }
}
