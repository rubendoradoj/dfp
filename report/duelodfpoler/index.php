<?php

use core\report_helper;

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->dirroot.'/notes/lib.php');

$id = required_param('id', PARAM_INT); // course id.

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

$output_head = html_writer::tag('h3', 'Envia tus preguntas', ['class' => 'contact-head-title']);

$output_head .= html_writer::tag('p', 'Recuerda que debes enviar el enunciado y 
                    las opciones de respuesta junto con la explicación 
                    retroalimentada de la respuesta correcta.
                    Cuando recibamos tu pregunta la revisaremos
                    y en el plazo de 5 días procederemos a subirla
                    al banco de preguntas de "Duelo DFPOLER" para que
                    el resto de alumnos pueda realizarla'
                    , ['class' => 'contact-summary']);

echo $output_head;

$output = '';

$attributes_form = [
    'method' => "post",
    'action' => "../../local/contact/index.php",
    'class' => 'contact-us',
];

$attributes_input_nombre = [
    'id' => 'name',
    'title' => "Minimo 3 letras/espacios",
    'maxlength' => "45",
    'name' => "name",
    'pattern'=>"[A-z&Agrave;-ž]([A-z&Agrave;-ž\s]){2,}",
    'type'=>"text",
    'value' => "",
];

$attributes_input_email = [
    'id' => 'email',
    'maxlength' => "60",
    'name' => "email",
    'type'=>"text",
    'value' => "",
    'required'=>"required",
    'size'=>"57",
];

$attributes_input_asunto = [
    'id' => 'subject',
    'title' => "Minimo 5 caracteres",
    'maxlength' => "80",
    'name' => "subject",
    'size'=>"57",
    'type'=>"text",
    'minlength'=>"5",
];

$attributes_input_mensaje = [
    'id'=>"message",
    'style'=>"border-radius: 4px; width: 100%;",
    'title'=>"M&iacute;nimo 5 caracteres.",
    'cols'=>"70",
    'name'=>"message",
    'required'=>"required",
    'rows'=>"10",
    'minlength'=>"5",
];

$attributes_input_button = [
    'id'=>"submit",
    'name'=>"submit",
    'type'=>"submit",
    'value'=>"Enviar",
    'class'=>"button",
];

$output .= html_writer::start_div('formulario-styles');

$output .= html_writer::tag('label', 'Tu nombre(obligatorio)', ['class' => 'contact-title', 'for' => 'name']);
$output .= html_writer::empty_tag('input', $attributes_input_nombre);

$output .= html_writer::tag('label', 'Correo electrónico(obligatorio)', ['class' => 'contact-title', 'for' => 'email']);
$output .= html_writer::empty_tag('input', $attributes_input_email);

$output .= html_writer::tag('label', 'Asunto(obligatorio)', ['class' => 'contact-title', 'for' => 'subject']);
$output .= html_writer::empty_tag('input', $attributes_input_asunto);

$output .= html_writer::tag('label', 'Mensaje(obligatorio)', ['class' => 'contact-title', 'for' => 'message']);
$output .= html_writer::tag('textarea', '', $attributes_input_mensaje);

$output .= html_writer::empty_tag('input', ['id'=>'sesskey', 'name'=>"sesskey", 'type'=>"hidden", 'value'=>""]);
$output .= html_writer::empty_tag('input', ['id'=>'recipient', 'name'=>"recipient", 'type'=>"hidden", 'value'=>"dfpoler"]);

$output .= html_writer::end_div('formulario-styles');

$fielset = html_writer::tag('fieldset', $output, ['class' => 'contact-fieldset']);
$fielset .= html_writer::empty_tag('input', $attributes_input_button);

$form = html_writer::tag('form', $fielset, $attributes_form);

echo $form;

echo $OUTPUT->footer();
