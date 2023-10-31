<?php

use core\report_helper;

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');

$id = required_param('id', PARAM_INT); // course id.

//$url = new moodle_url('/report/configuradortest/index.php', array('id'=>$id));

//$PAGE->set_url($url);
$PAGE->set_pagelayout('course');

$myNavigationNode = $PAGE->navbar->add('Configurador de Test');

//$PAGE->secondarynav->make_active();


/*print_r("---- ");
print_r($PAGE->secondarynav->action_link_actions());
print_r("----2 ");
print_r($PAGE->secondarynav->get_children_key_list());
print_r("----3 ");
print_r($PAGE->secondarynav->get_siblings());
print_r("----4 ");*/
print_r($PAGE->secondarynav);

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception('invalidcourse');
}

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_title("Configurador de Test");
$PAGE->set_heading(format_string($course->fullname, true, array('context' => $context)), true);
echo $OUTPUT->header();

echo $OUTPUT->footer();
