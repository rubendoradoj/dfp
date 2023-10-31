<?php

use core\report_helper;

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');

$id = required_param('id', PARAM_INT); // course id.

//$url = new moodle_url('/report/duelodfpoler/index.php', array('id'=>$id));

//$PAGE->set_url($url);
$PAGE->set_pagelayout('course');

$PAGE->navbar->add('Duelo DFPoler');

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    throw new \moodle_exception('invalidcourse');
}

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_title("Duelo DFPoler");
$PAGE->set_heading(format_string($course->fullname, true, array('context' => $context)), true);
echo $OUTPUT->header();

echo $OUTPUT->footer();

echo "<script>
    var menus = document.getElementsByClassName('nav-link');
    var miElemento = null;
    
    for (var i = 0; i < menus.length; i++) {
      if (menus[i].ariaLabel == 'Duelo DFPOLER') {
          miElemento = menus[i];
          miElemento.classList.add('active')
          break;
      }
    }
</script>";