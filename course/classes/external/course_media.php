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
 * Class for exporting a course module summary from an stdClass.
 *
 * @package    core_course
 * @copyright  2023 Miguel Rojas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\external;
defined('MOODLE_INTERNAL') || die();

use mod_quiz\quiz_settings;
use mod_quiz\quiz_attempt;
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/config.php');
/**
 * Class for exporting a course module summary from a cm_info class.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_media extends \core\external\exporter {

    /**
     * Get media of student by course.
     *
     * @return number
     */
    public static function get_media_by_course($courseId, $userId) {
        global $DB, $USER;
        
        $sum_medias_by_quiz = 0; 
        $count_quiz = 0;
        $data_grades = [];

        $course = $DB->get_record_sql("
                SELECT *
                FROM {course} c
                WHERE c.id = " . $courseId);
        
        /*Calculo de Media por alumno en cada curso */
        $all_attempts = array();
        $activities = \course_modinfo::get_array_of_activities($course);
        foreach($activities as $key1 => $value1){
            if(is_object($value1)){
                if($value1->visible==true){
                    if ($value1->mod === 'quiz'){
                        $quizobj_new = quiz_settings::create($value1->id);
                        $quiz_new = $quizobj_new->get_quiz();
                        $data_grades = \core_course\external\course_media::get_sum_grades_by_quiz($userId, $quiz_new->id, $quiz_new);

                        //Obtener sumatoria de medias en el curso
                        $sum_medias_by_quiz += $data_grades[0];
                        $count_quiz += $data_grades[1]; 
                    }
                }
            }
        }
        
        $media_by_course = $count_quiz > 0 ? $sum_medias_by_quiz/$count_quiz: 1;
        
        return round($media_by_course, 2);
    }

    /**
     * Get mobject bye quiz.
     *
     * @return Object
     */
    public static function get_object_by_quiz($quizId) {
        return quiz_settings::create($quizId);
    }

    /**
     * Get description by test realizados.
     *
     * @return Object
     */
    public static function get_description_by_quiz($courseId, $userId) {
        global $DB, $PAGE, $OUTPUT;

        $result_final = [];
        $course = $DB->get_record_sql("
                SELECT *
                FROM {course} c
                WHERE c.id = " . $courseId);

        $count = 0;
        $activities = \course_modinfo::get_array_of_activities($course);
        foreach($activities as $key1 => $value1){
            
            
            if(is_object($value1)){
                
                if($value1->visible == true){
                    if ($value1->mod === 'quiz'){   
                        $quizobj_new = quiz_settings::create($value1->id);
                        $quiz_new = $quizobj_new->get_quiz(); 
                        
                        $quizes_attempts = $DB->get_records_sql("
                            SELECT *
                            FROM {quiz_attempts} qa
                            WHERE qa.quiz = ".$value1->id." AND qa.userid = ".$userId."");
                        
                        if(count($quizes_attempts) > 0){

                            foreach ($quizes_attempts as $attempt) {
                                $result = new \stdClass;
                                $attemptId = $attempt->id;
                                $quiz_date = $attempt->timefinish;
                                $quiz_state = $attempt->state;
                                $cmid = $DB->get_record('course_modules', array('module' => '17', 'instance' => $value1->id));

                                if($quiz_state == quiz_attempt::FINISHED){
                                    $name = self::attempt_date($quiz_date, $value1->name);
                                    $result->name = $name;

                                    $correctas = self::get_all_success_answer_by_attempt_id($attemptId);
                                    $flags_correct = self::get_flags_by_correct_questions($attemptId);
                                    $flags_wrong = self::get_flags_by_wrong_questions($attemptId);

                                    $result->aciertos = $correctas->aciertos - $flags_correct;
                                    $result->fallos = $correctas->fallos - $flags_wrong;
                                    $result->pendientes = $correctas->pendientes;

                                    $calculo_nota_real = $attempt->sumgrades > 0 ? (($correctas->aciertos - $flags_correct) - (($correctas->fallos - $flags_wrong) / 2)) * 10 / $quiz_new->sumgrades : 0;

                                    $sum = quiz_format_grade($quiz_new, $calculo_nota_real);
                                    $result->nota = $sum;

                                    $result->attemptId = $attemptId;
                                    $result->cmid = $cmid->id;
                                    array_push($result_final, $result);
                                }
                            }
                        }
                        
                    }
                }
            }
        }

        $reversed = array_reverse($result_final);
        return $reversed;
    }

    /**
     * Generate a brief textual description of the current state of an attempt.
     *
     * @param quiz_attempt $attemptobj the attempt
     * @return string the appropriate lang string to describe the state.
     */
    public static function attempt_date($date, $name) {
        return '<div><p style="margin-bottom: 0.25rem;">'.$name.'</p><p style="font-size: 10px">'.get_string('statefinisheddetails', 'quiz',
        userdate($date)).'</p></div>';
    }

    /**
     * Get media DFPool in course.
     *
     * @return number
     */
    public static function get_media_dfpool_by_course($courseId) {
        global $DB;
        $media_by_student = 0;
        $media_by_course = 0;

        /*Calculo de Media opr alumno en cada curso */
        $users = $DB->get_records_sql("
                SELECT ue.userid
                FROM {user_enrolments} ue
                JOIN {enrol} en ON ue.enrolid = en.id
                JOIN {user} uu ON uu.id = ue.userid
                WHERE en.courseid = " . $courseId);

        foreach ($users as $user) {
            $media_by_course += self::get_media_by_course($user->userid, $courseId);
        }

        $media_by_student = count($users) > 0 ? $media_by_course / count($users) : 0;

        return round($media_by_student, 2);
    }

    /**
     * Get the number of flags in correct questions.
     *
     * @return number
     */
    public static function get_flags_by_correct_questions($attemptId) {
        global $DB;
        $flags = (object)$DB->get_record_sql('SELECT COUNT(qa.questionid) AS cantidades
                FROM {quiz_attempts} quiza
                JOIN {question_usages} qu ON qu.id = quiza.uniqueid
                JOIN {question_attempts} qa ON qa.questionusageid = qu.id
                JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
                LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid = qas.id
                WHERE qas.state IN ("gradedright") && flagged = 1
                GROUP BY quiza.id, quiza.quiz, qas.state, quiza.userid
                having quiza.id = ' . $attemptId);
        
        if(isset($flags->cantidades)) return $flags->cantidades;
    }

    /**
     * Get the number of flags in wrong questions.
     *
     * @return number
     */
    public static function get_flags_by_wrong_questions($attemptId) {
        global $DB;
        $flags = (object)$DB->get_record_sql('SELECT COUNT(qa.questionid) AS cantidades
                FROM {quiz_attempts} quiza
                JOIN {question_usages} qu ON qu.id = quiza.uniqueid
                JOIN {question_attempts} qa ON qa.questionusageid = qu.id
                JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
                LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid = qas.id
                WHERE qas.state IN ("gradedwrong") && flagged = 1
                GROUP BY quiza.id, quiza.quiz, qas.state, quiza.userid
                having quiza.id = ' . $attemptId);
        
        if(isset($flags->cantidades)) return $flags->cantidades;
    }

    /**
     * Get alls attempts by course id and quiz id.
     *
     * @return array
     */
    public static function get_media_by_quiz($userId, $quizId, $quiz) {
        global $DB;

        $sum_total = 0;

        $quizes_attempts = $DB->get_records_sql("
                SELECT *
                FROM {quiz_attempts} qa
                WHERE qa.quiz = ".$quizId." AND qa.userid = ".$userId."");

        foreach ($quizes_attempts as $attempt) {
            $correctas = self::get_all_success_answer_by_attempt_id($attempt->id);
            $flags_correct = self::get_flags_by_correct_questions($attempt->id);
            $flags_wrong = self::get_flags_by_wrong_questions($attempt->id);
            $nota_final = $quiz->sumgrades > 0 ? (($correctas->aciertos - $flags_correct) - (($correctas->fallos - $flags_wrong) / 2)) * $quiz->grade / $quiz->sumgrades : 0;
            $sum_total += $nota_final;
        }

        $media_by_quiz = count($quizes_attempts) > 0 ? $sum_total / count($quizes_attempts) : 0;

        return $media_by_quiz;
    }

    /**
     * Get sum grades and count by quiz.
     *
     * @return array
     */
    public static function get_sum_grades_by_quiz($userId, $quizId, $quiz) {
        global $DB;

        $sum_total = 0;
        $count = 0;

        $quizes_attempts = $DB->get_records_sql("
                SELECT *
                FROM {quiz_attempts} qa
                WHERE qa.quiz = ".$quizId." AND qa.userid = ".$userId."");

        foreach ($quizes_attempts as $attempt) {
            $correctas = self::get_all_success_answer_by_attempt_id($attempt->id);
            $flags_correct = self::get_flags_by_correct_questions($attempt->id);
            $flags_wrong = self::get_flags_by_wrong_questions($attempt->id);
            $nota_final = $quiz->sumgrades > 0 ? (($correctas->aciertos - $flags_correct) - (($correctas->fallos - $flags_wrong) / 2)) * $quiz->grade / $quiz->sumgrades : 0;
            $sum_total += $nota_final;
            $count += 1;
        }

        return [$sum_total, $count];
    }

    /**
     * Get alls test succe by course id and quiz id.
     *
     * @return array
     */
    public static function get_calification_by_test($userId) {
        global $DB, $COURSE;

        $all_attempts = array();
        $count = 0;
        $sum_approved = 0;
        $sum_suspended = 0;
        $activities = \course_modinfo::get_array_of_activities($COURSE); /*cambio nuevo -----*/
        foreach($activities as $key1 => $value1){
            if(is_object($value1)){
                if($value1->visible==true){
                    if ($value1->mod == 'quiz'){
                        $quizobj_new = quiz_settings::create($value1->id);
                        $quiz_new = $quizobj_new->get_quiz();       
                        
                        $quizes_attempts = $DB->get_records_sql("
                            SELECT *
                            FROM {quiz_attempts} qa
                            WHERE qa.quiz = ".$quiz_new->id." AND qa.userid = ".$userId."");
                        
                        foreach ($quizes_attempts as $attempt) {
                            $correctas = self::get_all_success_answer_by_attempt_id($attempt->id);
                            $flags_correct = self::get_flags_by_correct_questions($attempt->id);
                            $flags_wrong = self::get_flags_by_wrong_questions($attempt->id);
                            $nota_final = $quiz_new->sumgrades > 0 ? (($correctas->aciertos - $flags_correct) - (($correctas->fallos - $flags_wrong) / 2)) * $quiz_new->grade / $quiz_new->sumgrades : 0;
                            if($nota_final >= 5) {
                                $sum_approved += 1;
                            } else {
                                $sum_suspended += 1;
                            }
                        }
                    }
                }
            }
        }

        $all_calification = [$sum_approved, $sum_suspended];

        return $all_calification;
    }


    /**
     * Get alls success answer by attempt id.
     *
     * @return array
     */
    public static function get_all_success_answer_by_attempt_id($attemptId) {
        global $DB;
        
        $amount = $DB->get_record_sql("
            SELECT quizattemptid, quiz, max(aciertos) aciertos, max(fallos) fallos, max(total) total, (max(total) - max(fallos) - max(aciertos)) AS pendientes  FROM
            (
                SELECT sub.quizattemptid, sub.quiz, if(sub.state = 'gradedright', sub.cantidad, 0) aciertos,
                if(sub.state = 'gradedwrong', sub.cantidad, 0) fallos,
                if(sub.state = 'todo', sub.cantidad, 0) total
                FROM 
                (
                SELECT COUNT(qa.questionid) AS cantidad, qas.state, quiza.quiz, quiza.id AS quizattemptid, quiza.userid
                FROM {quiz_attempts} quiza
                JOIN {question_usages} qu ON qu.id = quiza.uniqueid
                JOIN {question_attempts} qa ON qa.questionusageid = qu.id
                JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
                LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid = qas.id
                WHERE qas.state IN ('gradedwrong', 'gradedright', 'todo')
                GROUP BY quiza.id, quiza.quiz, qas.state, quiza.userid
                ) sub
            ) parent
            GROUP BY quizattemptid, quiz
            having quizattemptid = " . $attemptId);

        return $amount;
    }

    /**
     * Get the total number of questions in the bank
     *
     * @return array
     */
    public static function get_total_questions_bank($courseId) {
        global $DB, $USER;
        
        $amount = $DB->get_record_sql("
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
                            SELECT COUNT(qa.questionid) AS cantidad, qas.state, quiza.quiz, quiza.id AS quizattemptid, quiza.userid
                            from {quiz} as q
                            join {quiz_attempts} AS quiza ON quiza.quiz = q.id
                            JOIN {question_usages} AS qu ON qu.id = quiza.uniqueid
                            JOIN {question_attempts} AS qa ON qa.questionusageid = qu.id
                            JOIN {question_attempt_steps} AS qas ON qas.questionattemptid = qa.id
                            LEFT JOIN {question_attempt_step_data} AS qasd ON qasd.attemptstepid = qas.id
                            where qas.state IN ('gradedwrong', 'gradedright', 'todo') && quiza.userid = ".$USER->id." && q.course = ". $courseId ." 
                            GROUP BY quiza.id, quiza.quiz, qas.state, quiza.userid
                        ) sub
                ) sub
                GROUP BY quizattemptid, quiz
            ) parent");

        return $amount;
    }

     /**
     * Get alls success answer by attempt id.
     *
     * @return array
     */
    public static function get_all_sections_by_course($courseId) {
        global $DB;
        
        $sections = $DB->get_records_sql("
            SELECT *
            FROM {course_sections} cs
            WHERE cs.course = " . $courseId);

        return $sections;
    }

    /**
     * Get alls questions by course.
     *
     * @return array
     */
    public static function get_all_questions_by_course($courseId) {
        global $DB;
        
        $questions = $DB->get_records_sql("
                    SELECT 
                        q.id as quizid, 
                        q.course, 
                        q.name, 
                        qa.id, 
                        qa.questionusageid, 
                        qa.slot, 
                        qa.questionid, 
                        qa.flagged, 
                        qa.questionsummary
                    FROM {quiz} as q
                    JOIN {quiz_attempts} AS quiza ON quiza.quiz = q.id
                    JOIN {question_usages} AS qu ON qu.id = quiza.uniqueid
                    JOIN {question_attempts} AS qa ON qa.questionusageid = qu.id
                    WHERE q.course = " . $courseId);

        return $questions;
    }

    /**
     * Get questions by quiz.
     *
     * @return array
     */
    public static function get_questions_by_quiz($courseId, $quizId, $userId, $dificultad, $tipo) {
        global $DB;

        $array_dificultad = explode(",", $dificultad);
        $array_tipo = explode(",", $tipo);
        $sql_dif = "qc.name LIKE '%facil%' || qc.name LIKE '%medio%' || qc.name LIKE '%dificil%'";
        $sql_tip = "qas.state LIKE '%todo%' || qas.state LIKE '%gradedwrong%'";
        $sql_flag = "";
        $sql_group_by = "qa.questionid";

        switch (count($array_dificultad)) {
            case 1:
                if(strtolower($array_dificultad[0]) != 'todos'){
                    $sql_dif = "qc.name LIKE '%".strtolower($array_dificultad[0])."%'";
                } else {
                    $sql_dif = "qc.name LIKE '%facil%' || qc.name LIKE '%medio%' || qc.name LIKE '%dificil%'";
                }
                break;
            case 2:
                $sql_dif = "qc.name LIKE '%".strtolower($array_dificultad[0])."%' || qc.name LIKE '%".strtolower($array_dificultad[1])."%'";
                break;
            case 3:
                $sql_dif = "qc.name LIKE '%facil%' || qc.name LIKE '%medio%' || qc.name LIKE '%dificil%'";
                break;
        }

        if(in_array("Falladas", $array_tipo) && count($array_tipo) == 1){
            $sql_tip = "qas.state LIKE '%gradedwrong%'";
        }

        if(in_array("Sin responder", $array_tipo) && count($array_tipo) == 1){
            $sql_tip = "qas.state LIKE '%todo%'";
        }

        if(in_array("Con riesgo", $array_tipo) && count($array_tipo) == 1){
            $sql_tip = "qas.state LIKE '%todo%'";
        }

        if(in_array("Todos", $array_tipo) && count($array_tipo) == 1){
            $sql_tip = "qas.state LIKE '%todo%' || qas.state LIKE '%gradedwrong%'";
            $sql_group_by = "qa.questionid, qas.state";
        }

        if(in_array("Sin responder", $array_tipo) && in_array("Falladas", $array_tipo)){
            $sql_tip = "qas.state LIKE '%todo%' || qas.state LIKE '%gradedwrong%'";
            $sql_group_by = "qa.questionid, qas.state";
        }

        if(in_array("Con riesgo", $array_tipo)){
            $sql_flag = "qa.flagged = 1";
        } else {
            $sql_flag = "qa.flagged = 0 || qa.flagged = 1";
        }

        $questions_by_quiz = $DB->get_records_sql("
            SELECT 
                qa.id as attemptid,
                qu.id as usageid,
                qa.questionid,
                qa.flagged,
                qas.userid,
                qas.state, 
                qc.name,
                quiza.id as quizaid,
                quiza.quiz
            FROM {question_categories} as qc
            JOIN {question_bank_entries} AS qbe ON qbe.questioncategoryid = qc.id
            JOIN {question_references} as qr on qr.questionbankentryid  = qbe.id
            JOIN {question_usages} as qu on qu.contextid = qr.usingcontextid
            JOIN {question_attempts} AS qa on qa.questionusageid = qu.id
            JOIN {question_attempt_steps} as qas on qas.questionattemptid = qa.id
            JOIN {quiz_attempts} AS quiza on quiza.userid = qas.userid
            JOIN {quiz} AS q on q.id = quiza.quiz
            WHERE 
                (".$sql_dif.") && 
                qas.userid = ".$userId." && 
                (".$sql_tip.") && 
                q.id = ".$quizId." && 
                q.course = ".$courseId." &&
                (".$sql_flag.")
            GROUP BY ". $sql_group_by);

        return $questions_by_quiz;
    }

    /**
     * Get all quizes by course.
     *
     * @return array
     */
    public static function get_quizes_by_course($courseId) {
        global $DB;
        
        $quizes = $DB->get_records_sql("
                    SELECT 
                        cm.id as cmid,
                        cm.instance as quizid, 
                        cs.name, 
                        cs.section
                    FROM {course_modules} AS cm
                    JOIN {course_sections} AS cs ON cs.id = cm.section
                    WHERE cm.module = 17 
                        && cs.section != 0 
                        && cm.course = ".$courseId);

        return $quizes;
    }

    /**
     * Get quizes by theme.
     *
     * @return array
     */
    public static function get_quizes_by_theme($courseId, $theme) {
        global $DB;
        
        $quizes = $DB->get_records_sql("
                    SELECT 
                        cm.id as cmid, 
                        cm.instance as quizid, 
                        cs.name, 
                        cs.section
                    FROM {course_modules} AS cm
                    JOIN {course_sections} AS cs ON cs.id = cm.section
                    WHERE cm.module = 17 
                        && cs.section != 0 
                        && cm.course = ".$courseId." 
                        && cs.section = " . $theme + 1);

        return $quizes;
    }

    /**
     * Get quiz by id.
     *
     * @return array
     */
    public static function get_quiz_by_id($quizId) {
        global $DB;
        
        $quiz = $DB->get_records_sql("
                    SELECT *
                    FROM {quiz} AS q
                    WHERE q.id = " . $quizId);

        return $quiz;
    }

    /**
     * Get test personalized.
     *
     * @return array
     */
    public static function get_test_personalized() {
        global $DB;
        
        $test_personalized = $DB->get_records_sql("
                    SELECT *
                    FROM {quiz} AS q
                    WHERE q.name LIKE '%personalizado%'");

        return $test_personalized;
    }
}