<?php

use core\report_helper;
use core_course\external\course_media;
use mod_quiz\quiz_settings;

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/modlib.php');

global $COURSE;
$id = required_param('id', PARAM_INT); // course id.
$cm = get_coursemodule_from_id('quiz', $id);

$PAGE->set_pagelayout('course');

$PAGE->navbar->add('Configurador de Test');

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception('invalidcourse');
}

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_title("Configurador de Test");
$PAGE->set_heading(format_string($course->fullname, true, array('context' => $context)), true);
echo $OUTPUT->header();

$sections = \core_course\external\course_media::get_all_sections_by_course($COURSE->id);

$array = new stdClass();
$array->generador = "Hola";
$questions = [];
$count_ini = 0;
if(count($sections) === 27) {$count_ini = 0;}
if(count($sections) === 12) {$count_ini = 26;}
if(count($sections) === 9) {$count_ini = 37;}
$total = count($sections) + $count_ini - 1;

for($i = $count_ini; $i < $total; $i++){
    $qst = new stdClass();
    $qst -> question = $i + 1;
    array_push($questions, $qst);
}

$array->id = $id;
$array->questions = $questions;

echo $OUTPUT->render_from_template('report_configuradortest/generador', $array);
$PAGE->requires->js('/report/configuradortest/javascript/radiodetect.js');
$PAGE->requires->js('/report/configuradortest/javascript/accionmodal.js');

echo $OUTPUT->footer();
