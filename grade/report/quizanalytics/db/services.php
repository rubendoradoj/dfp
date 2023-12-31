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
 * services for the quizanalytics gradebook report
 * @package   gradereport_quizanalytics
 * @author DualCube <admin@dualcube.com>
 * @copyright  Dualcube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$services = array(
    'moodle_gradereport_quizanalytics' => array(
        'functions' => array('moodle_quizanalytics_analytic'),
        'requiredcapability' => '',
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);
$functions = array(
    'moodle_quizanalytics_analytic' => array(
        'classname' => 'moodle_gradereport_quizanalytics_external',
        'methodname' => 'quizanalytics_analytic',
        'classpath' => 'grade/report/quizanalytics/externallib.php',
        'description' => 'Get Analytics data',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true
    )
);
