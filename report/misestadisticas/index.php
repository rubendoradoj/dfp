<?php

use core\report_helper;
use core_course\external\course_media;

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');

global $DB, $COURSE, $USER;
$id = required_param('id', PARAM_INT); // course id.

//$url = new moodle_url('/report/misestadisticas/index.php', array('id'=>$id));

//$PAGE->set_url($url);
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
$output .= html_writer::start_div('cards-styles');

$output .= html_writer::start_div('card-style');
$output .= html_writer::tag('h1','Total de Preguntas', array('class' => 'card-chart-title'));

$questions_bank = \core_course\external\course_media::get_total_questions_bank($COURSE->id);
$sum_total_questions = $questions_bank->aciertos + $questions_bank->fallos + $questions_bank->pendientes;
$output .= html_writer::tag('p', $sum_total_questions, array('class' => 'card-chart-number'));
$output .= html_writer::end_div('card-style');

$output .= html_writer::start_div('card-style');
$output .= html_writer::tag('h1','Aciertos', array('class' => 'card-chart-title'));
$output .= html_writer::tag('p', $questions_bank->aciertos > 0 ? $questions_bank->aciertos : 0, array('class' => 'card-chart-number'));
$output .= html_writer::end_div('card-style');

$output .= html_writer::start_div('card-style');
$output .= html_writer::tag('h1','Fallos', array('class' => 'card-chart-title'));
$output .= html_writer::tag('p', $questions_bank->fallos > 0 ? $questions_bank->fallos : 0, array('class' => 'card-chart-number'));
$output .= html_writer::end_div('card-style');

$output .= html_writer::start_div('card-style');
$output .= html_writer::tag('h1','Pendientes', array('class' => 'card-chart-title'));
$output .= html_writer::tag('p', $questions_bank->pendientes > 0 ? $questions_bank->pendientes : 0, array('class' => 'card-chart-number'));
$output .= html_writer::end_div('card-style');

$output .= html_writer::end_div('cards-styles');


/*Graficas */
$output .= html_writer::start_div('chart-styles');

/* Total de test realizados */
$cantidad_quiz = $DB->get_record_sql("
        SELECT COUNT(qui.id) AS cantidad
        FROM {quiz} qui
        LEFT JOIN {quiz_attempts} AS qu ON qu.quiz = qui.id
        WHERE qu.userid = ".$USER->id." AND qui.course = " . $course->id);

$render_cantidad = new \core\chart_series('Total Test Realizados', [$cantidad_quiz->cantidad > 0 ? $cantidad_quiz->cantidad : 0]);
$labels_total = ['Total Test Realizados'];

$chart_total = new \core\chart_pie();
$chart_total->set_doughnut(true);
$chart_total->add_series($render_cantidad);
$chart_total->set_labels($labels_total);
$output .= html_writer::tag('div', $OUTPUT->render_chart($chart_total,false), ['dir' => 'ltr']);

/* Nota media vs DFPool */
$media_by_course = \core_course\external\course_media::get_media_by_course($COURSE->id, $USER->id);
$media_DFPool_by_course = \core_course\external\course_media::get_media_dfpool_by_course($COURSE->id);

$serie_media_by_course = new \core\chart_series('Media', [$media_by_course, 0]);
$serie_media_DFPool_by_course = new \core\chart_series('Media DFPool', [0, $media_DFPool_by_course]);
$labels_medias = ['Media', 'Media DFPool'];

$chart_medias = new core\chart_bar();
$chart_medias->add_series($serie_media_by_course);
$chart_medias->add_series($serie_media_DFPool_by_course);
$chart_medias->set_labels($labels_medias);
$output .= html_writer::tag('div', $OUTPUT->render_chart($chart_medias,false), ['dir' => 'ltr']);

/* Total de test acertadas, falladas y sin responder */
$result_quiz = $DB->get_record_sql("
        SELECT
            SUM(aciertos) as aciertos,
            SUM(fallos) as fallos,
            SUM(pendientes) as pendientes
            FROM (
                SELECT  quizattemptid, 
                        quiz,
                        max(aciertos) aciertos, 
                        max(fallos) fallos, 
                        max(total) total, 
                        (max(total) - max(fallos) - max(aciertos)) AS pendientes  
                FROM
                (
                    SELECT sub.quizattemptid, sub.quiz, 
                    if(sub.state = 'gradedright', sub.cantidad, 0) aciertos,
                    if(sub.state = 'gradedwrong', sub.cantidad, 0) fallos,
                    if(sub.state = 'todo', sub.cantidad, 0) total
                    FROM 
                        (
                            SELECT COUNT(qa.questionid) AS cantidad, qas.state, quiza.quiz, quiza.id AS quizattemptid, quiza.userid /*count(qu.quiz) as cantidad , qu.quiz*/
                            from {quiz} as q
                            join {quiz_attempts} AS quiza ON quiza.quiz = q.id
                            JOIN {question_usages} AS qu ON qu.id = quiza.uniqueid
                            JOIN {question_attempts} AS qa ON qa.questionusageid = qu.id
                            JOIN {question_attempt_steps} AS qas ON qas.questionattemptid = qa.id
                            LEFT JOIN {question_attempt_step_data} AS qasd ON qasd.attemptstepid = qas.id
                            where qas.state IN ('gradedwrong', 'gradedright', 'todo') && quiza.userid = ".$USER->id." && q.course = ".$course->id."
                            GROUP BY quiza.id, quiza.quiz, qas.state, quiza.userid
                        ) sub
                ) sub
                GROUP BY quizattemptid, quiz
        ) parent");

$render_aciertos = new \core\chart_series('Acertadas', [$result_quiz->aciertos]);
$render_fallos = new \core\chart_series('Falladas', [$result_quiz->fallos]);
$render_pendientes = new \core\chart_series('Pendientes', [$result_quiz->pendientes]);
$labels_result = ['Preguntas'];

$chart_result = new core\chart_bar();
$chart_result->set_horizontal(true);
$chart_result->add_series($render_aciertos);
$chart_result->add_series($render_fallos);
$chart_result->add_series($render_pendientes);
$chart_result->set_labels($labels_result);
$output .= html_writer::tag('div', $OUTPUT->render_chart($chart_result,false), ['dir' => 'ltr']);

/* Porcentaje de test acertadas, falladas y sin responder */

$total_preguntas = $result_quiz->aciertos + $result_quiz->fallos + $result_quiz->pendientes;
$porcentaje_aciertos = $total_preguntas > 0 ? $result_quiz->aciertos * 100 / $total_preguntas: 0;
$porcentaje_fallos = $total_preguntas > 0 ? $result_quiz->fallos * 100 / $total_preguntas : 0;
$porcentaje_pendientes = $total_preguntas > 0 ? $result_quiz->pendientes * 100 / $total_preguntas : 0;

$series_porcentajes = new \core\chart_series('Porcentaje', [round($porcentaje_aciertos), round($porcentaje_fallos), round($porcentaje_pendientes)]);
$labels_porcentajes = ['Aciertos', 'Fallos', 'Pendientes'];

$chart_porcent = new \core\chart_pie();
$chart_porcent->add_series($series_porcentajes);
$chart_porcent->set_labels($labels_porcentajes);
$output .= html_writer::tag('div', $OUTPUT->render_chart($chart_porcent,false), ['dir' => 'ltr']);

$output .= html_writer::end_div();

echo $output;

echo $OUTPUT->footer();

echo "<script>
    var menus = document.getElementsByClassName('nav-link');
    var miElemento = null;
    
    for (var i = 0; i < menus.length; i++) {
      if (menus[i].ariaLabel == 'Mis estadísticas') {
          miElemento = menus[i];
          miElemento.classList.add('active')
          break;
      }
    }
</script>";