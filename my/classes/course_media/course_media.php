<?php
namespace core_my\course_media;

use core_course\external\course_media;

require('../../../config.php');

redirect_if_major_upgrade_required();

global $USER, $CFG;
require_login();

require_once($CFG->dirroot . '/course/classes/external/course_media.php');

if( isset($_GET['courseid']) ) {
    if($_GET['courseid']) {
        $id = intval($_GET['courseid']);
        $media_by_user= \core_course\external\course_media::get_media_by_course($id, $USER->id);
        $media_dfp = \core_course\external\course_media::get_media_dfpool_by_course($id);
        $jsondata['success'] = true;
        $jsondata['media'] = $media_by_user;
        $jsondata['media_dfp'] = $media_dfp;
    } else {
        $jsondata['success'] = false;
    }

    header('Content-type: application/json; charset=utf-8');
    echo json_encode($jsondata);
    exit();

}
