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
// along with Moodle.  If not, see <http://www.gnu.org/license

require_once('../../config.php');
//require_once('lib.php');
require_once($CFG->libdir.'/completionlib.php');


$id = optional_param('id', 0, PARAM_INT);
$cancelQuizAttempt = optional_param('cancelQuizAttempt', -1 , PARAM_INT);
$quizId = optional_param('quizId', -1 , PARAM_INT);

$params = [];
if (!empty($name)) {
    $params = ['shortname' => $name];
} else if (!empty($idnumber)) {
    $params = ['idnumber' => $idnumber];
} else if (!empty($id)) {
    $params = ['id' => $id];
} else {
    throw new \moodle_exception('unspecifycourseid', 'error');
}

$course = $DB->get_record('course', $params, '*', MUST_EXIST);

$urlparams = ['id' => $course->id];

if ($cancelQuizAttempt > 0) {
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');
    //Create quiz object
    //$quizobj = \mod_quiz\quiz_attempt::create($cancelQuizAttempt);
    $quizobj = \mod_quiz\quiz_settings::create($quizId);
    quiz_delete_attempt($cancelQuizAttempt, $quizobj->get_quiz());
     redirect(new moodle_url('/course/view.php?id=' . $course->id));
}