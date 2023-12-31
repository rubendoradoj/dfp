<?php

use core\report_helper;
use core_course\external\course_media;
use mod_quiz\quiz_settings;

require_once('../../../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/modlib.php');

global $COURSE, $DB, $CFG, $USER;

require_login($COURSE);

$quiz_personalizado = count(\core_course\external\course_media::get_test_personalized());

$now = new DateTime();
$new_quiz = new stdClass();
$new_quiz->modulename = 'quiz';
$new_quiz->module = 17;
$new_quiz->course = $COURSE->id;
$new_quiz->name = "Test personalizado " . $quiz_personalizado;
$new_quiz->intro = "";
$new_quiz->introformat = 1;
$new_quiz->timeopen = 0;
$new_quiz->timeclose = 0;
$new_quiz->overduehandling = "autosubmit";
$new_quiz->graceperiod = 0;
$new_quiz->preferredbehaviour = "deferredfeedback";
$new_quiz->canredoquestions = 0;
$new_quiz->attempts = 0;
$new_quiz->attemptonlast = 0;
$new_quiz->grademethod = 4;
$new_quiz->decimalpoints = 2;
$new_quiz->questiondecimalpoints = -1;
$new_quiz->reviewattempt = 69888;
$new_quiz->reviewcorrectness = 4352;
$new_quiz->reviewmarks = 4352;
$new_quiz->reviewspecificfeedback = 4352;
$new_quiz->reviewgeneralfeedback = 4352;
$new_quiz->reviewrightanswer = 4352;
$new_quiz->reviewoverallfeedback = 4352;
$new_quiz->questionsperpage = 0;
$new_quiz->navmethod = "free";
$new_quiz->shuffleanswers = 1;
$new_quiz->sumgrades = 15;
$new_quiz->grade = 10;
$new_quiz->timecreated = time();
$new_quiz->timemodified = time();
$new_quiz->quizpassword = "";
$new_quiz->subnet = "";
$new_quiz->browsersecurity = "-";
$new_quiz->delay1 = 0;
$new_quiz->delay2 = 0;
$new_quiz->showuserpicture = 0;
$new_quiz->showblocks = 0;
$new_quiz->completionattemptsexhausted = 0;
$new_quiz->completionminattempts = 0;
$new_quiz->allowofflineattemps = 0;
$new_quiz->visible = 1;
$new_quiz->visibleoncoursepage = 1;
$new_quiz->section = 1;
$new_quiz->cmidnumber = null;

if (isset($_POST['state'])) {
    $courseid = $_POST['id'];
    $temas = $_POST['temas'];
    $dificultad = $_POST['dificultad'];
    $tipo = $_POST['tipo'];
    $cantidad = $_POST['cantidad'];
    $duracion = $_POST['duracion'];
    $state = $_POST['state'];
    $maximoPreguntas = 0;

    if($duracion == "sintiempo"){
        $new_quiz->timelimit = 0;
    } else {
        $new_quiz->timelimit = intval($duracion)*60;
    }

    $quizes_by_theme = array();
    $questions_by_quiz = array();
    $questions_all = array();
    $array_temas = explode(",", $temas);

    foreach ($array_temas as $tema) {
        array_push($quizes_by_theme, \core_course\external\course_media::get_quizes_by_theme($courseid, $tema));
    }

    foreach ($quizes_by_theme as $quiz) {
        foreach ($quiz as $quiz_obj) {
            if($quiz_obj->quizid){
                array_push(
                    $questions_by_quiz, 
                    \core_course\external\course_media::get_questions_by_quiz(
                        $courseid, 
                        $quiz_obj->quizid, 
                        $USER->id,
                        $dificultad,
                        $tipo
                    )
                );
            } 
        }
    }

    foreach ($questions_by_quiz as $questions) {
        foreach ($questions as $question) {
            array_push($questions_all, $question);
        }
    }

    $maximoPreguntas = intval(count($questions_all));
    $position = 0;

    if($state == 'read') { echo $maximoPreguntas; }
    if($state == 'create') {
        $course_data = $DB->get_record('course', array('id'=>$courseid));
        $quiz_ret = add_moduleinfo($new_quiz, $course_data);
        $quizobj_new = \core_course\external\course_media::get_object_by_quiz($quiz_ret->id);
        $quiz_new = $quizobj_new->get_quiz();

        if(intval($cantidad) > $maximoPreguntas){
            for($i = 0; $i < $maximoPreguntas; $i++){
                quiz_add_quiz_question($questions_all[$i]->questionid, $quiz_new);
            }
        } else {
            for($i = 0; $i < $cantidad; $i++){
                $position = rand(0, $maximoPreguntas);
                quiz_add_quiz_question($questions_all[$position]->questionid, $quiz_new);
            }
        }

        $cmm = get_coursemodule_from_instance('quiz', $quiz_ret->id);
        $url_re = $CFG->wwwroot . '/mod/quiz/startattempt.php?cmid=' . $cmm->id . '&sesskey=' . sesskey();
        
        echo $url_re;
    };
}