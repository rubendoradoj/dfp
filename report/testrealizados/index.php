<?php

use core\report_helper;

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');

global $DB, $USER;

$id = required_param('id', PARAM_INT); // course id.

$PAGE->set_pagelayout('course');

$PAGE->navbar->add('Test realizados');

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception('invalidcourse');
}

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_title("Test realizados");
$PAGE->set_heading(format_string($course->fullname, true, array('context' => $context)), true);
echo $OUTPUT->header();

$getDescription = \core_course\external\course_media::get_description_by_quiz($course->id, $USER->id);

$table = new html_table();
$table->attributes['class'] = 'generaltable rui-quizattemptsummary mt-2 mb-0';
$table->head = array();
$table->align = array();
$table->size = array();

$table->head[] = 'Título y fecha';
$table->align[] = 'left';
$table->size[] = '';

$table->head[] = 'Aciertos';
$table->align[] = 'right';
$table->head[] = 'Fallos';
$table->align[] = 'right';
$table->head[] = 'Sin responder';
$table->align[] = 'right';

$table->head[] = 'Calificación/10,00';
$table->align[] = 'right';
$table->size[] = '';

$table->head[] = get_string('review', 'quiz');
$table->align[] = 'right';
$table->size[] = '';
$row = array();

foreach ($getDescription as $description) {
    $link = new moodle_url('/mod/quiz/review.php', ['attempt' => $description->attemptId, 'cmid' => $description->cmid]);
    $revision = '<a title="Revisar sus respuestas en este intento" href="'.$link.'">Revisión</a>';
    
    $table->data[] = array(
                        $description->name, 
                        $description->aciertos, 
                        $description->fallos, 
                        $description->pendientes,
                        $description->nota,
                        $revision);
}
$table->data['preview'] = $row;

$output = '';
$output .= html_writer::tag('h2', 'Test realizados', array('class' => 'test-realizados-title'));
$output .= html_writer::start_tag('div', array('class' => 'table-overflow mb-4'));
$output .= html_writer::table($table);
$output .= html_writer::end_tag('div');

echo $output;

echo $OUTPUT->footer();
