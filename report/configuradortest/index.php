<?php

use core\report_helper;

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');

$id = required_param('id', PARAM_INT); // course id.

$PAGE->set_pagelayout('course');

$PAGE->navbar->add('Configurador de Test');

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception('invalidcourse');
}

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_title("Configurador de Test");
$PAGE->set_heading(format_string($course->fullname, true, array('context' => $context)), true);

/*print_r("------- ");
print_r($PAGE->navigation);*/

/*print_r("-------2 ");
print_r($PAGE->navbar);*/

print_r("-------3 ");
print_r($PAGE->secondarynav);
/*
print_r("-------4 ");
print_r($PAGE->primarynav);

print_r("-------5 ");
print_r($PAGE->settingsnav);

print_r("-------6 ");
print_r($PAGE->primarynavcombined);*/



echo $OUTPUT->header();

echo $OUTPUT->footer();