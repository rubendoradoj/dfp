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
 * misestadisticas report
 *
 * @package    report
 * @subpackage misestadisticas
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\report_helper;

require('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');
//require_once($CFG->dirroot.'/report/misestadisticas/locallib.php');

$participantsperpage = intval(get_config('moodlecourse', 'participantsperpage'));
define('DEFAULT_PAGE_SIZE', (!empty($participantsperpage) ? $participantsperpage : 20));
define('SHOW_ALL_PAGE_SIZE', 5000);

$id         = required_param('id', PARAM_INT); // course id.
$texto     = optional_param('texto', '', PARAM_RAW);
$roleid     = optional_param('roleid', 0, PARAM_INT); // which role to show
$instanceid = optional_param('instanceid', 0, PARAM_INT); // instance we're looking at.
$timefrom   = optional_param('timefrom', 0, PARAM_INT); // how far back to look...
$action     = optional_param('action', '', PARAM_ALPHA);
$page       = optional_param('page', 0, PARAM_INT);                     // which page to show
$perpage    = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);  // how many per page
$currentgroup = optional_param('group', null, PARAM_INT); // Get the active group.

$url = new moodle_url('/report/misestadisticas/index.php', array('id'=>$id));
if ($roleid !== 0) $url->param('roleid');
if ($instanceid !== 0) $url->param('instanceid');
if ($timefrom !== 0) $url->param('timefrom');
if ($action !== '') $url->param('action');
if ($page !== 0) $url->param('page');
if ($perpage !== DEFAULT_PAGE_SIZE) $url->param('perpage');
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

if ($action != 'view' and $action != 'post') {
    $action = ''; // default to all (don't restrict)
}

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception('invalidcourse');
}

if ($roleid != 0 and !$role = $DB->get_record('role', array('id'=>$roleid))) {
    throw new \moodle_exception('invalidrole');
}

require_login($course);
$context = context_course::instance($course->id);
//require_capability('report/participation:view', $context);

$strmisestadisticas = get_string('misestadisticasreport');
$strviews         = get_string('views');
$strposts         = get_string('posts');
$strreports       = get_string('reports');

//$actionoptions = report_misestadisticas_get_action_options();
//if (!array_key_exists($action, $actionoptions)) {
    $action = '';
//}

$PAGE->set_title(format_string($course->shortname, true, array('context' => $context)) .': '. $strmisestadisticas);
$PAGE->set_heading(format_string($course->fullname, true, array('context' => $context)));
echo $OUTPUT->header();

// Print the selector dropdown.
//$pluginname = get_string('pluginname', 'report_misestadisticas');
//report_helper::print_report_selector($pluginname);

// Logs will not have been recorded before the course timecreated time.
$minlog = $course->timecreated;
$onlyuselegacyreader = false; // Use only legacy log table to aggregate records.

/*$logtable = report_misestadisticas_get_log_table_name(); // Log table to use for fetaching records.

// If no log table, then use legacy records.
if (empty($logtable)) {
    $onlyuselegacyreader = true;
}*/

$modinfo = get_fast_modinfo($course);

// Print first controls.
//report_misestadisticas_print_filter_form($course, $timefrom, $minlog, $action, $roleid, $instanceid);

$baseurl = new moodle_url('/report/misestadisticas/index.php', array(
    'id' => $course->id,
    'roleid' => $roleid,
    'instanceid' => $instanceid,
    'timefrom' => $timefrom,
    'action' => $action,
    'perpage' => $perpage,
    'group' => $currentgroup
));

echo "<h1>" . $texto . "</h1>";



echo $OUTPUT->footer();
