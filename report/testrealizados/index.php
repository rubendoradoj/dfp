<?php

use core\report_helper;

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');

$id = required_param('id', PARAM_INT); // course id.

//$url = new moodle_url('/report/testrealizados/index.php', array('id'=>$id));

//$PAGE->set_url($url);
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

//$table->head[] = get_string('marks', 'quiz') . ' / ' . quiz_format_grade($quiz, $quiz->sumgrades);
$table->head[] = 'Calificación/10,00';
$table->align[] = 'right';
$table->size[] = '';

$table->head[] = get_string('review', 'quiz');
$table->align[] = 'right';
$table->size[] = '';

$row = array();
$row[] = '<div><p style="margin-bottom: 0.25rem;">Cuestionario 1.1</p><p style="font-size: 10px">Enviado: martes, 10 de octubre de 2023, 11:26</p></div>';
$row[] = 12;
$row[] = 4;
$row[] = 0;
$row[] = 7.5;
$row[] = 'Revision';

$table->data['preview'] = $row;

$output = '';
$output .= html_writer::tag('h2', 'Test realizados', array('class' => 'test-realizados-title'));
$output .= html_writer::start_tag('div', array('class' => 'table-overflow mb-4'));
$output .= html_writer::table($table);
$output .= html_writer::end_tag('div');

echo $output;

echo $OUTPUT->footer();
