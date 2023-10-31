<?php

use core\report_helper;

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');

$id = required_param('id', PARAM_INT); // course id.

//$url = new moodle_url('/report/configuradortest/index.php', array('id'=>$id));

//$PAGE->set_url($url);
$PAGE->set_pagelayout('course');

$myNavigationNode = $PAGE->navbar->add('Configurador de Test2');
$myNavigationNode->make_active();

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception('invalidcourse');
}

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_title("Configurador de Testxx");
$PAGE->set_heading(format_string($course->fullname, true, array('context' => $context)), true);
echo $OUTPUT->header();

echo $OUTPUT->footer();
