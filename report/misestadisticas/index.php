<?php

use core\report_helper;
use core_course\external\course_media;

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');

global $DB, $COURSE, $USER;
$id = required_param('id', PARAM_INT); // course id.

$PAGE->set_pagelayout('course');

$PAGE->navbar->add('Mis Estadísticas');

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception('invalidcourse');
}

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_title("Mis Estadísticas");
$PAGE->set_heading(format_string($course->fullname, true, array('context' => $context)), true);
echo $OUTPUT->header();

/* Inicio de graficos */
$CFG->chart_colorset = ['#002db3', '#3357c2', '#8096d9', '#b3c0e8', '#ccd5f0'];
$output = '';

/*Cards de metricas */

$output .= html_writer::start_div('metricas-styles');

$output .= html_writer::start_div('cards-styles');
/* Total de preguntas */
$output .= html_writer::start_div('card-style');

$output .= html_writer::start_div('card-style-top');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-question fa-4x card-style-icon']);
$output .= html_writer::start_div('card-style-text');
$questions_bank = \core_course\external\course_media::get_total_questions_bank($COURSE->id);
$sum_total_questions = $questions_bank->aciertos + $questions_bank->fallos + $questions_bank->pendientes;
$output .= html_writer::tag('h1', $sum_total_questions, array('class' => 'card-chart-number'));
$output .= html_writer::tag('h1','Total de Preguntas', array('class' => 'card-chart-title'));
$output .= html_writer::end_div('card-style-text');
$output .= html_writer::end_div('card-style-top');

$output .= html_writer::start_div('card-style-bottom');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-bar-chart card-style-sub-icon']);
$output .= html_writer::tag('p','por curso', array('class' => 'card-chart-sub-title'));
$output .= html_writer::end_div('card-style-bottom');

$output .= html_writer::end_div('card-style');

/* Total de preguntas acertadas */
$output .= html_writer::start_div('card-style success');

$output .= html_writer::start_div('card-style-top');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-check fa-4x card-style-icon success-text']);
$output .= html_writer::start_div('card-style-text');
$output .= html_writer::tag('h1', $questions_bank->aciertos > 0 ? $questions_bank->aciertos : 0, array('class' => 'card-chart-number success-text'));
$output .= html_writer::tag('h1','Aciertos', array('class' => 'card-chart-title success-text'));
$output .= html_writer::end_div('card-style-text');
$output .= html_writer::end_div('card-style-top');

$output .= html_writer::start_div('card-style-bottom');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-bar-chart card-style-sub-icon success-text']);
$output .= html_writer::tag('p','por curso', array('class' => 'card-chart-sub-title success-text'));
$output .= html_writer::end_div('card-style-bottom');

$output .= html_writer::end_div('card-style');

/* Total de preguntas falladas */
$output .= html_writer::start_div('card-style danger');

$output .= html_writer::start_div('card-style-top');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-times fa-4x card-style-icon danger-text']);
$output .= html_writer::start_div('card-style-text');
$output .= html_writer::tag('h1', $questions_bank->fallos > 0 ? $questions_bank->fallos : 0, array('class' => 'card-chart-number danger-text'));
$output .= html_writer::tag('h1','Fallos', array('class' => 'card-chart-title danger-text'));
$output .= html_writer::end_div('card-style-text');
$output .= html_writer::end_div('card-style-top');

$output .= html_writer::start_div('card-style-bottom');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-bar-chart card-style-sub-icon danger-text']);
$output .= html_writer::tag('p','por curso', array('class' => 'card-chart-sub-title danger-text'));
$output .= html_writer::end_div('card-style-bottom');

$output .= html_writer::end_div('card-style');

/* Total de preguntas sin responder */
$output .= html_writer::start_div('card-style pending');

$output .= html_writer::start_div('card-style-top');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-refresh fa-4x card-style-icon pending-text']);
$output .= html_writer::start_div('card-style-text');
$output .= html_writer::tag('h1', $questions_bank->pendientes > 0 ? $questions_bank->pendientes : 0, array('class' => 'card-chart-number pending-text'));
$output .= html_writer::tag('h1','Sin responder', array('class' => 'card-chart-title pending-text'));
$output .= html_writer::end_div('card-style-text');
$output .= html_writer::end_div('card-style-top');

$output .= html_writer::start_div('card-style-bottom');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-bar-chart card-style-sub-icon pending-text']);
$output .= html_writer::tag('p','por curso', array('class' => 'card-chart-sub-title pending-text'));
$output .= html_writer::end_div('card-style-bottom');

$output .= html_writer::end_div('card-style');

$output .= html_writer::end_div('cards-styles');

/*Graficas */
$output .= html_writer::start_div('chart-styles');

/* Porcentaje de test acertadas, falladas y sin responder */
$result_quiz = \core_course\external\course_media::get_total_questions_bank($COURSE->id);

$total_preguntas = $result_quiz->aciertos + $result_quiz->fallos + $result_quiz->pendientes;
$porcentaje_aciertos = $total_preguntas > 0 ? $result_quiz->aciertos * 100 / $total_preguntas: 0;
$porcentaje_fallos = $total_preguntas > 0 ? $result_quiz->fallos * 100 / $total_preguntas : 0;
$porcentaje_pendientes = $total_preguntas > 0 ? $result_quiz->pendientes * 100 / $total_preguntas : 0;

$series_porcentajes = new \core\chart_series('Porcentaje', [round($porcentaje_aciertos), round($porcentaje_fallos), round($porcentaje_pendientes)]);
$labels_porcentajes = ["Aciertos: ".round($porcentaje_aciertos, 2)." %", "Fallos: ".round($porcentaje_fallos,2)." %", "Sin responder: ".round($porcentaje_pendientes,2)." %"];

$chart_porcent = new \core\chart_pie();
$chart_porcent->add_series($series_porcentajes);
$chart_porcent->set_labels($labels_porcentajes);
$output .= html_writer::tag('div', $OUTPUT->render_chart($chart_porcent,false), ['dir' => 'ltr']);

$output .= html_writer::end_div('chart-styles');

$output .= html_writer::end_div('metricas-styles');
/*Fin Primera Fila */

/*Inicio Segunda Fila */
$output .= html_writer::start_div('cards-styles');

/* Total de test aprobadas, abandonadas */
$all_test = \core_course\external\course_media::get_calification_by_test($USER->id);
/* Total de test realizados */
$output .= html_writer::start_div('card-style');

$output .= html_writer::start_div('card-style-top');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-file fa-4x card-style-icon']);
$output .= html_writer::start_div('card-style-text');
$output .= html_writer::tag('h1', count($all_test) > 0 ? round($all_test[0]) + round($all_test[1]) : 0, array('class' => 'card-chart-number'));
$output .= html_writer::tag('h1','Total de Tests', array('class' => 'card-chart-title'));
$output .= html_writer::end_div('card-style-text');
$output .= html_writer::end_div('card-style-top');

$output .= html_writer::start_div('card-style-bottom');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-bar-chart card-style-sub-icon']);
$output .= html_writer::tag('p','por curso', array('class' => 'card-chart-sub-title'));
$output .= html_writer::end_div('card-style-bottom');

$output .= html_writer::end_div('card-style');

/* Total de test aprobadas */
$output .= html_writer::start_div('card-style');

$output .= html_writer::start_div('card-style-top');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-check fa-4x card-style-icon']);
$output .= html_writer::start_div('card-style-text');
$output .= html_writer::tag('h1', count($all_test) > 0 ? round($all_test[0]) : 0, array('class' => 'card-chart-number'));
$output .= html_writer::tag('h1','Tests Aprobados', array('class' => 'card-chart-title'));
$output .= html_writer::end_div('card-style-text');
$output .= html_writer::end_div('card-style-top');

$output .= html_writer::start_div('card-style-bottom');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-bar-chart card-style-sub-icon']);
$output .= html_writer::tag('p','por curso', array('class' => 'card-chart-sub-title'));
$output .= html_writer::end_div('card-style-bottom');

$output .= html_writer::end_div('card-style');

/* Total de test abandonadas */
$output .= html_writer::start_div('card-style');

$output .= html_writer::start_div('card-style-top');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-times fa-4x card-style-icon']);
$output .= html_writer::start_div('card-style-text');
$output .= html_writer::tag('h1', count($all_test) > 0 ? round($all_test[1]) : 0, array('class' => 'card-chart-number'));
$output .= html_writer::tag('h1','Tests Suspendidos', array('class' => 'card-chart-title'));
$output .= html_writer::end_div('card-style-text');
$output .= html_writer::end_div('card-style-top');

$output .= html_writer::start_div('card-style-bottom');
$output .= html_writer::tag('i', '', ['class' => 'fa fa-bar-chart card-style-sub-icon']);
$output .= html_writer::tag('p','por curso', array('class' => 'card-chart-sub-title'));
$output .= html_writer::end_div('card-style-bottom');

$output .= html_writer::end_div('card-style');

$output .= html_writer::end_div('cards-styles');

echo $output;

echo $OUTPUT->footer();
