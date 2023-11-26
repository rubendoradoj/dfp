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
 * This page prints a review of a particular quiz attempt
 *
 * It is used either by the student whose attempts this is, after the attempt,
 * or by a teacher reviewing another's attempt during or afterwards.
 *
 * @package   mod_quiz
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\output\navigation_panel_review;
use mod_quiz\output\renderer;
use mod_quiz\quiz_attempt;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');

$attemptid = required_param('attempt', PARAM_INT);
$page      = optional_param('page', 0, PARAM_INT);
$showall   = optional_param('showall', null, PARAM_BOOL);
$cmid      = optional_param('cmid', null, PARAM_INT);

$url = new moodle_url('/mod/quiz/review.php', ['attempt' => $attemptid]);
if ($page !== 0) {
    $url->param('page', $page);
} else if ($showall) {
    $url->param('showall', $showall);
}
$PAGE->set_url($url);
$PAGE->set_secondary_active_tab("modulepage");

$attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);
$attemptobj->preload_all_attempt_step_users();
$page = $attemptobj->force_page_number_into_range($page);

// Now we can validate the params better, re-genrate the page URL.
if ($showall === null) {
    $showall = $page == 0 && $attemptobj->get_default_show_all('review');
}
$PAGE->set_url($attemptobj->review_url(null, $page, $showall));

// Check login.
require_login($attemptobj->get_course(), false, $attemptobj->get_cm());
$attemptobj->check_review_capability();

// Create an object to manage all the other (non-roles) access rules.
$accessmanager = $attemptobj->get_access_manager(time());
$accessmanager->setup_attempt_page($PAGE);

$options = $attemptobj->get_display_options(true);

// Check permissions - warning there is similar code in reviewquestion.php and
// quiz_attempt::check_file_access. If you change on, change them all.
if ($attemptobj->is_own_attempt()) {
    if (!$attemptobj->is_finished()) {
        redirect($attemptobj->attempt_url(null, $page));

    } else if (!$options->attempt) {
        $accessmanager->back_to_view_page($PAGE->get_renderer('mod_quiz'),
                $attemptobj->cannot_review_message());
    }

} else if (!$attemptobj->is_review_allowed()) {
    throw new moodle_exception('noreviewattempt', 'quiz', $attemptobj->view_url());
}

// Load the questions and states needed by this page.
if ($showall) {
    $questionids = $attemptobj->get_slots();
} else {
    $questionids = $attemptobj->get_slots($page);
}

// Save the flag states, if they are being changed.
if ($options->flags == question_display_options::EDITABLE && optional_param('savingflags', false,
        PARAM_BOOL)) {
    require_sesskey();
    $attemptobj->save_question_flags();
    redirect($attemptobj->review_url(null, $page, $showall));
}

// Work out appropriate title and whether blocks should be shown.
if ($attemptobj->is_own_preview()) {
    navigation_node::override_active_url($attemptobj->start_attempt_url());

} else {
    if (empty($attemptobj->get_quiz()->showblocks) && !$attemptobj->is_preview_user()) {
        $PAGE->blocks->show_only_fake_blocks();
    }
}

// Set up the page header.
$headtags = $attemptobj->get_html_head_contributions($page, $showall);
$PAGE->set_title($attemptobj->review_page_title($page, $showall));
$PAGE->set_heading($attemptobj->get_course()->fullname);
$PAGE->activityheader->disable();

// Summary table start. ============================================================================

// Work out some time-related things.
$attempt = $attemptobj->get_attempt();
$quiz = $attemptobj->get_quiz();
$overtime = 0;

if ($attempt->state == quiz_attempt::FINISHED) {
    if ($timetaken = ($attempt->timefinish - $attempt->timestart)) {
        if ($quiz->timelimit && $timetaken > ($quiz->timelimit + 60)) {
            $overtime = $timetaken - $quiz->timelimit;
            $overtime = format_time($overtime);
        }
        $timetaken = format_time($timetaken);
    } else {
        $timetaken = "-";
    }
} else {
    $timetaken = get_string('unfinished', 'quiz');
}


if ($attempt->state == quiz_attempt::FINISHED) {
    $summarydata['timetaken'] = [
        'title'   => get_string('timetaken', 'quiz'),
        'content' => $timetaken,
    ];
}

if (!empty($overtime)) {
    $summarydata['overdue'] = [
        'title'   => get_string('overdue', 'quiz'),
        'content' => $overtime,
    ];
}

// Any additional summary data from the behaviour.
$summarydata = array_merge($summarydata, $attemptobj->get_additional_summary_data($options));

// Feedback if there is any, and the user is allowed to see it now.
$feedback = $attemptobj->get_overall_feedback($grade);
if ($options->overallfeedback && $feedback) {
    $summarydata['feedback'] = [
        'title'   => get_string('feedback', 'quiz'),
        'content' => $feedback,
    ];
}

// Summary table end. ==============================================================================

if ($showall) {
    $slots = $attemptobj->get_slots();
    $lastpage = true;
} else {
    $slots = $attemptobj->get_slots($page);
    $lastpage = $attemptobj->is_last_page($page);
}

/** @var renderer $output */
$output = $PAGE->get_renderer('mod_quiz');

// Arrange for the navigation to be displayed.
$navbc = $attemptobj->get_navigation_panel($output, navigation_panel_review::class, $page, $showall);
$regions = $PAGE->blocks->get_regions();
$PAGE->blocks->add_fake_block($navbc, reset($regions));

echo $output->review_page($attemptobj, $slots, $page, $showall, $lastpage, $options, $summarydata);

// Trigger an event for this review.
$attemptobj->fire_attempt_reviewed_event();

$contentNav = $navbc->content;
$totalQuantity = substr_count( $contentNav, 'quiznavbutton');
$quantityMark = substr_count( $contentNav, 'flagged' );

$pattern = "/\"(qnbutton)(.*?)\"/i";
preg_match_all($pattern, $contentNav, $resultVal, PREG_OFFSET_CAPTURE);

foreach ($resultVal[2] as &$valor) {
    $evaluateMark = substr_count( $valor[0], 'flagged' );
    if($evaluateMark === 0){
        $quantityIncorrectReal += substr_count( $valor[0], 'incorrect' );
        $quantityNotAnsweredReal += substr_count( $valor[0], 'notanswered' );
        $quantityCorrectReal += substr_count( $valor[0], ' correct ' );
    }
}
$quantityNotAnsweredReal += $quantityMark;

$calculo_real = ($quantityCorrectReal - ($quantityIncorrectReal / 2)) * 10 / $totalQuantity;
$finalScoreReal = number_format($calculo_real, 2, '.', '' );

// NOTA ARRIESGANDO
$quantityIncorrectRisk = substr_count( $contentNav, 'incorrect' );
$quantityNotAnsweredRisk = substr_count( $contentNav, 'notanswered' );
$quantityCorrectRisk = substr_count( $contentNav, ' correct ' );

$calculo_riesgo = ($quantityCorrectRisk - ($quantityIncorrectRisk / 2)) * 10 / $totalQuantity;
$finalScoreRisk = number_format($calculo_riesgo, 2, '.', '' );

echo '
<script>
    function hideOptions(){
        const infoSection = document.getElementsByClassName("info");
        const grades = document.getElementsByClassName("grade");
        for (let div of infoSection){
            const sectionMark = div.children[1];
            const markQuestion = sectionMark.children[0];
            markQuestion.classList.remove("d-inline-flex");
            markQuestion.style.display = "none";
        }
        for (let div of grades){
            div.style.display = "none";
        }
    }
    
    function createBoxScore(){
        const boxScore = document.createElement("div");
        const divContent = document.createElement("div");
        boxScore.setAttribute("class", "d-flex");
        divContent.setAttribute("class", "box_custom");
        divContent.classList.add("move_lef_custom");
        boxScore.appendChild(divContent);
        insertCustomScore(divContent);
        const mainContentSummary = document.getElementsByClassName("rui-summary-table")[0];
        if(!mainContentSummary){
           divContent.style.marginTop = "76px";     
        }
        return boxScore;
    }
    
    function createBoxRisking(){
        const boxRisking = document.createElement("div");
        const divContent = document.createElement("div");
        boxRisking.setAttribute("class", "d-flex");
        divContent.setAttribute("class", "box_custom");
        divContent.classList.add("move_right_custom");
        boxRisking.appendChild(divContent);
        insertCustomRisking(divContent);
        const mainContentSummary = document.getElementsByClassName("rui-summary-table")[0];
        if(!mainContentSummary){
           divContent.style.marginTop = "70px";     
        }
        return boxRisking;
    }
    
    function insertBoxes(){
        const boxScore = createBoxScore();
        const boxRisking = createBoxRisking();
        const mainContentSummary = document.getElementsByClassName("rui-summary-table")[0];
        const headerContent = document.getElementsByClassName("wrapper-header")[0];
        const mainContent = document.getElementsByClassName("main-content")[0];
        if(mainContentSummary){
            mainContentSummary.classList.add("d-flex");
            const divTime = mainContentSummary.children[0];
            divTime.parentNode.insertBefore(boxScore, divTime.nextSibling);
            divTime.parentNode.insertBefore(boxRisking, divTime.nextSibling);
        } else if(headerContent){
            headerContent.classList.add("d-flex");
            const divTime = headerContent.children[0];
            divTime.parentNode.insertBefore(boxScore, divTime.nextSibling);
            divTime.parentNode.insertBefore(boxRisking, divTime.nextSibling);  
            mainContent.classList.add("margin_custom_content");
        }
    }
    
    function insertCustomScore(nodeParent){
        const allHtml = "<h5 class=\'custom_title\'>Nota Real</h5><div class=\'custom_line\'></div><div class=\'custom_total\'>'. $finalScoreReal .'</div><h5 class=\'custom_title_green\'>Aciertos: '. $quantityCorrectReal .'</h5><h5 class=\'custon_title_normal\'>Sin Contestar: "+'. $quantityNotAnsweredReal .'+"</h5><h5 class=\'custom_title_red\'>Errores: '. $quantityIncorrectReal .'</h5>";
        nodeParent.insertAdjacentHTML("beforeend", allHtml);
    }
    
    function insertCustomRisking(nodeParent){
        const allHtml = "<h5 class=\'custom_title\'>Nota Arriesgando</h5><div class=\'custom_line\'></div><div class=\'custom_total\'>'. $finalScoreRisk .'</div><h5 class=\'custom_title_green\'>Aciertos: '. $quantityCorrectRisk .'</h5><h5 class=\'custon_title_normal\'>Sin Contestar: "+'. $quantityNotAnsweredRisk .'+"</h5><h5 class=\'custom_title_red\'>Errores: '. $quantityIncorrectRisk .'</h5><h5 class=\'custom_title_risk\'>Con riesgo: '. $quantityMark .'</h5>";
        nodeParent.insertAdjacentHTML("beforeend", allHtml);
    }
    
    function fixOrderNavQuestionarie(){
        const navQuestionarieSinglePage = document.getElementsByClassName("qn_buttons clearfix allquestionsononepage")[0];
        const navQuestionarieMultipage = document.getElementsByClassName("qn_buttons clearfix multipages")[0];
        if(navQuestionarieSinglePage){
            navQuestionarieSinglePage.style.gridGap = "0";
        }
        if(navQuestionarieMultipage){
            navQuestionarieMultipage.style.gridGap = "0";
        }
    }
    
    hideOptions();
    insertBoxes();
    fixOrderNavQuestionarie();
    
    let _numPregunta = "";
    let _cuestionario = "";
    let _textoPregunta = "";
    let _mensaje = "";
    let _duda = false;
    function mostrarModal(numPregunta, cuestionario, textoPregunta, duda){
        _numPregunta = numPregunta;
        _cuestionario = cuestionario;
        _textoPregunta = textoPregunta;
        _duda = duda;
        const modal = document.getElementById("modal_finish_all");
        const backdrop = document.getElementById("backdrop_modal_finish");
        modal.classList.add("show");
        backdrop.classList.add("show");
        modal.classList.remove("hide");
        backdrop.classList.remove("hide");
        document.getElementById("mensajeAlumno").value = "";
        
        if (duda === true) {
            document.getElementById("modalTitulo").innerHTML = "Dudas";
            document.getElementById("modalTexto").innerHTML = "Indícanos tu duda y te la resolveremos lo antes posible:";
            document.getElementById("modalBoton").innerHTML = "Enviar duda";
        } else {
            document.getElementById("modalTitulo").innerHTML = "Impugnar pregunta";
            document.getElementById("modalTexto").innerHTML = "Argumenta el motivo para impugnar esta pregunta;";
            document.getElementById("modalBoton").innerHTML = "Impugnar";
        }
    }
    
    function ocultarModal(){
        const modal = document.getElementById("modal_finish_all");
        const backdrop = document.getElementById("backdrop_modal_finish");
        modal.classList.remove("show");
        backdrop.classList.remove("show");
        modal.classList.add("hide");
        backdrop.classList.add("hide");
    }
 
function ajaxEnviarEmail() {
    // Create an object to hold the data to be sent in the request body
    var data = {
        number: _numPregunta,
        cuestionario: _cuestionario,
        textoPregunta: _textoPregunta,
        mensaje: document.getElementById("mensajeAlumno").value,
        duda: _duda
    };

    // Convert the data to JSON format
    var jsonData = JSON.stringify(data);

    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();

    // Define the URL and HTTP method for the request
    var url = "sendemail.php"; // Replace with your custom URL
    xhr.open("POST", url, true);

    // Set the content type to JSON
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

    // Define a callback function to handle the response
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Request was successful, and you can handle the response here
            var response = xhr.responseText;
        }
    };

    // Send the JSON data in the request body
    xhr.send(jsonData);
    
    ocultarModal();
}
    
</script>
<style>
    .box_custom{
        position: absolute;
        left: 0;
        right: 0;
        margin: 0 auto;
        width: 300px;
        border: 2px solid #E1E1E1;
        border-radius: 5px;
    }
    .custom_title{
        text-align: center;
        margin-top: 8px;
        color: #212121;
    }
    .custom_title_score_risk{
        color: #feedba59;
    }
    .custom_title_green {
        margin: .5rem;
        border-radius: 4px;
        text-align: center;
        color: #027654;
        background-color: #a3dbb6;
    }
    .custon_title_normal {
        text-align: center;
        margin: 0 .5rem .5rem .5rem;
        border-radius: 4px;
        background-color: #f5f5f5;
    }
    .custom_title_red {
        text-align: center;
        margin: 0 .5rem .5rem .5rem;
        border-radius: 4px;
        color: #910808;
        background-color: #feeded;
    }
    .custom_title_risk {
        text-align: center;
        margin: 0 .5rem .5rem .5rem;
        border-radius: 4px;
        color: #212121;
        background-color: #feedba;
    }
    .custom_line{
        width: auto;
        border: 1px solid;
        opacity: .5;
        margin: 10px;
    }
    .custom_total{
        width: 90%;
        height: 30px;
        margin: 0 auto;
        padding: 4px;
        border-radius: 6px;
        color: white;
        background-color: #002db3;
        text-align: center;
    }
    .move_lef_custom{
        transform: translate(-160px, 0)
    }
    .move_right_custom{
        transform: translate(160px, 0)
    }
    .margin_custom_content{
        margin-top: 200px;
    }
    #mod_quiz_navblock .qnbutton.notanswered{
        color: #002db3;
    }
    #mod_quiz_navblock .qnbutton.notanswered .trafficlight{
        background-color: transparent;
    }
    #mod_quiz_navblock .qnbutton.notanswered .thispageholder{
        border-color: #002db3;
    }
    #mod_quiz_navblock .qnbutton{
        border-radius: 9px;
    }
    #mod_quiz_navblock .qnbutton .thispageholder{
        border-width: 1px;
        border-radius: 9px;
    }
    .qn_buttons{
        margin: 0 5px;
       justify-content: unset; 
    }
    #mod_quiz_navblock .qnbutton{
        width: 32px;
        height: 32px;
    }
    
    /* Extra small devices (phones, 600px and down) */
    @media only screen and (max-width: 600px) {
        .rui-quizreviewsummary .rui-infobox{
            margin-top: 380px;
        }
        .move_lef_custom {
            width: 220px;
            transform: translate(0, 0);
        }
        .move_right_custom {
            width: 220px;
            transform: translate(0, 200px);
        }
        .margin_custom_content{
            margin-top: 400px;
        }
    }
    
    /* Small devices (portrait tablets and large phones, 600px and up) */
    @media only screen and (min-width: 600px) {
        .rui-quizreviewsummary .rui-infobox{
            margin-top: 190px;
        }
        .move_lef_custom {
            transform: translate(-140px, 0);
            width: 260px;
        }
        .move_right_custom {
            transform: translate(140px, 0);
            width: 260px;
        }
    }
    
    /* Medium devices (landscape tablets, 768px and up) */
    @media only screen and (min-width: 768px) {
        .move_lef_custom {
            transform: translate(-160px, 0);
            width: 300px;
        }
        .move_right_custom {
            transform: translate(160px, 0);
            width: 300px;
        }
    } 
    
    /* Large devices (laptops/desktops, 992px and up) */
    @media only screen and (min-width: 992px) {
    
    } 
    p
    /* Extra large devices (large laptops and desktops, 1200px and up) */
    @media only screen and (min-width: 1200px) {
    
    }
    
    .modal-body{
        font-size: 0;
    }
    .modal-body:after{
        content:"¿Estas seguro de que quieres cancelar el test? El progreso se perderá y no se guardará."; 
        font-size: 16px;
    }
    .modal-content{
        height: max-content !important;
        margin-top: 50%;
        margin-bottom: 50%;
    }
    .modal-title{
        font-size: 0;
    }
    .modal-title:after{
        content:"Cancelar cuestionario"; 
        font-weight: 600;
        font-size: 16px;
    }
    .modal-title-special{
        color: #212121 !important;
    }
    .modal-body-special{
        padding: 16px;
    }
    /* Extra small devices (phones, 600px and down) */
    @media only screen and (max-width: 600px) {
        .modal-content{
            margin-top: 24%;
        }
    }
    
</style>

<div>
    <div id="backdrop_modal_finish" class="modal-backdrop in hide" aria-hidden="true" data-region="modal-backdrop" style="z-index: 1253;"></div>
</div>

<div>
    <div tabindex="-1" style="position: fixed; top: 0px; left: 0px;" aria-hidden="true" data-aria-hidden-tab-index="0"></div>
        <div id="modal_finish_all" class="modal moodle-has-zindex hide" data-region="modal-container" role="dialog" tabindex="-1" style="z-index: 1254;">
        <div class="modal-dialog modal-dialog-scrollable" role="document" data-region="modal" aria-labelledby="modal-title" tabindex="0">
            <div class="modal-content">
                <div class="modal-header " data-region="header">
                    <h5 class="modal-title-special" data-region="title" id="modalTitulo">Impugnar pregunta</h5>
                    <button type="button" class="close"  onClick="ocultarModal()" data-action="hide" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body-special" data-region="body" style="">
                    <div class="quiz-submission-confirmation-content">
                        <div class="mb-3" id="modalTexto">
                            Argumenta el motivo para impugnar esta pregunta
                        </div>
                        <div>
                            <textarea id="mensajeAlumno" cols="55" style="width: 100%;" maxlength="1000" rows="6"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" data-region="footer">
    
        <div class="w-100 d-flex justify-content-between">
            <button type="button" class="btn btn-primary" onclick="ajaxEnviarEmail()" id="modalBoton">Impugnar</button>
            <button type="button" class="btn btn-secondary" onClick="ocultarModal()" data-action="cancel">Cancelar</button>
        </div>
                    </div>
            </div>
        </div>
    </div>
    <div tabindex="-1" style="position: fixed; top: 0px; left: 0px;" aria-hidden="true" data-aria-hidden-tab-index="0"></div>
</div>
';

