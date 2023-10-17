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
 * Contains the default activity item from a section.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace format_onetopic\output\courseformat;

class activitybadge extends \core_courseformat\output\activitybadge {

    protected function update_content(): void {
        global $CFG;

        require_once($CFG->dirroot . '/mod/forum/lib.php');

        if (forum_tp_can_track_forums()) {
            if ($unread = forum_tp_count_forum_unread_posts($this->cminfo, $this->cminfo->get_course())) {
                if ($unread == 1) {
                    $this->content = get_string('unreadpostsone', 'forum');
                } else {
                    $this->content = get_string('unreadpostsnumber', 'forum', $unread);
                }
                $this->style = self::STYLES['dark'];
            }
        }
    }

final public function export_for_template(renderer_base $output): stdClass {
        $this->update_content();
        if (empty($this->content)) {
            return new stdClass();
        }

        $data = (object)[
            'badgecontent' => $this->content,
            'badgestyle' => $this->style,
        ];

        if (!empty($this->url)) {
            $data->badgeurl = $this->url->out();
        }

        if (!empty($this->elementid)) {
            $data->badgeelementid = $this->elementid;
        }

        if (!empty($this->extraattributes)) {
            $data->badgeextraattributes = $this->extraattributes;
        }

        return $data;
    }
	
    /**
     * Creates an instance of activityclass for the given course module, in clase it implements it.
     *
     * @param cm_info $cminfo
     * @return self|null An instance of activityclass for the given module or null if the module doesn't implement it.
     */
    final public static function create_instance(cm_info $cminfo): ?self {
        $classname = '\mod_' . $cminfo->modname . '\output\activitybadge';
        if (!class_exists($classname)) {
            return null;
        }
        return new $classname($cminfo);
    }	
}


