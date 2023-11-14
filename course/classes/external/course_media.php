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
require_once($CFG->dirroot . '/course/lib.php');

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
        global $DB;

        /*Calculo de Media por alumno en cada curso */
        $all_attempts = array();
        $activities = get_array_of_activities($courseId);
        foreach($activities as $key1 => $value1){
            if(is_object($value1)){
                if($value1->visible==true){
                    if ($value1->mod == 'quiz'){
                        $quizobj_new = quiz_settings::create($value1->id);
                        $quiz_new = $quizobj_new->get_quiz();                        

                        /*Obtener sumatoria de medias en el curso */
                        $sum_medias_by_quiz += \core_course\external\course_media::get_media_by_quiz($userId, $quiz_new->id, $quiz_new);
                    }
                }
            }
        }

        $media_by_course = count($activities) > 0 ? $sum_medias_by_quiz/count($activities) : 0;

        return round($media_by_course, 2);
    }

    /**
     * Get media DFPool in course.
     *
     * @return number
     */
    public static function get_media_dfpool_by_course($courseId) {
        global $DB;

        /*Calculo de Media opr alumno en cada curso */
        $users = $DB->get_records_sql("
                SELECT ue.userid
                FROM {user_enrolments} ue
                JOIN {enrol} en ON ue.enrolid = en.id
                JOIN {user} uu ON uu.id = ue.userid
                WHERE en.courseid = " . $courseId);

        foreach ($users as $user) {
            $media_by_student += self::get_media_by_course($courseId, $user->userid);;
        }

        return round($media_by_student, 2);
    }

    /**
     * Get the number of flags in correct questions.
     *
     * @return number
     */
    public static function get_flags_by_correct_questions($attemptId) {
        global $DB;

        $flags = $DB->get_record_sql("
                SELECT 
                    COUNT(qa.questionid) AS cantidad, 
                    qas.state, 
                    quiza.quiz, 
                    quiza.id AS quizattemptid, 
                    quiza.userid, 
                    qa.flagged AS flag
                FROM {quiz_attempts} quiza
                JOIN {question_usages} qu ON qu.id = quiza.uniqueid
                JOIN {question_attempts} qa ON qa.questionusageid = qu.id
                JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
                LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid = qas.id
                WHERE qas.state IN ('gradedright') && flagged = 1
                GROUP BY quiza.id, quiza.quiz, qas.state, quiza.userid
                having quizattemptid = " . $attemptId);

        return $flags->cantidad;
    }

    /**
     * Get alls attempts by course id and quiz id.
     *
     * @return array
     */
    public static function get_media_by_quiz($userId, $quizId, $quiz) {
        global $DB;

        $quizes_attempts = $DB->get_records_sql("
                SELECT *
                FROM {quiz_attempts} qa
                WHERE qa.quiz = ".$quizId." AND qa.userid = ".$userId."");

        foreach ($quizes_attempts as $attempt) {
            $correctas = self::get_all_success_answer_by_attempt_id($attempt->id);
            $flags_correct = self::get_flags_by_correct_questions($attempt->id);
            $nota_final = $quiz->sumgrades > 0 ? (intval($correctas->aciertos) - $flags_correct)  * $quiz->grade / $quiz->sumgrades : 0;
            $sum_total += $nota_final;
        }

        $media_by_quiz = count($quizes_attempts) > 0 ? $sum_total / count($quizes_attempts) : 0;

        return $media_by_quiz;
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
        global $DB;
        
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
                            where qas.state IN ('gradedwrong', 'gradedright', 'todo') && q.course = ". $courseId ." 
                            GROUP BY quiza.id, quiza.quiz, qas.state, quiza.userid
                        ) sub
                ) sub
                GROUP BY quizattemptid, quiz
            ) parent");

        return $amount;
    }
}