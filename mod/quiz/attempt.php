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
 * This script displays a particular page of a quiz attempt that is in progress.
 *
 * @package   mod_quiz
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\output\navigation_panel_attempt;
use mod_quiz\output\renderer;
use mod_quiz\quiz_attempt;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// Look for old-style URLs, such as may be in the logs, and redirect them to startattemtp.php.
if ($id = optional_param('id', 0, PARAM_INT)) {
    redirect($CFG->wwwroot . '/mod/quiz/startattempt.php?cmid=' . $id . '&sesskey=' . sesskey());
} else if ($qid = optional_param('q', 0, PARAM_INT)) {
    if (!$cm = get_coursemodule_from_instance('quiz', $qid)) {
        throw new \moodle_exception('invalidquizid', 'quiz');
    }
    redirect(new moodle_url('/mod/quiz/startattempt.php',
            ['cmid' => $cm->id, 'sesskey' => sesskey()]));
}

// Get submitted parameters.
$attemptid = required_param('attempt', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$cmid = optional_param('cmid', null, PARAM_INT);

$attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);
$page = $attemptobj->force_page_number_into_range($page);
$PAGE->set_url($attemptobj->attempt_url(null, $page));
// During quiz attempts, the browser back/forwards buttons should force a reload.
$PAGE->set_cacheable(false);

$PAGE->set_secondary_active_tab("modulepage");

// Check login.
require_login($attemptobj->get_course(), false, $attemptobj->get_cm());

// Check that this attempt belongs to this user.
if ($attemptobj->get_userid() != $USER->id) {
    if ($attemptobj->has_capability('mod/quiz:viewreports')) {
        redirect($attemptobj->review_url(null, $page));
    } else {
        throw new moodle_exception('notyourattempt', 'quiz', $attemptobj->view_url());
    }
}

// Check capabilities and block settings.
if (!$attemptobj->is_preview_user()) {
    $attemptobj->require_capability('mod/quiz:attempt');
    if (empty($attemptobj->get_quiz()->showblocks)) {
        $PAGE->blocks->show_only_fake_blocks();
    }

} else {
    navigation_node::override_active_url($attemptobj->start_attempt_url());
}

// If the attempt is already closed, send them to the review page.
if ($attemptobj->is_finished()) {
    redirect($attemptobj->review_url(null, $page));
} else if ($attemptobj->get_state() == quiz_attempt::OVERDUE) {
    redirect($attemptobj->summary_url());
}

// Check the access rules.
$accessmanager = $attemptobj->get_access_manager(time());
$accessmanager->setup_attempt_page($PAGE);
/** @var renderer $output */
$output = $PAGE->get_renderer('mod_quiz');
$messages = $accessmanager->prevent_access();
if (!$attemptobj->is_preview_user() && $messages) {
    throw new \moodle_exception('attempterror', 'quiz', $attemptobj->view_url(),
            $output->access_messages($messages));
}
if ($accessmanager->is_preflight_check_required($attemptobj->get_attemptid())) {
    redirect($attemptobj->start_attempt_url(null, $page));
}

// Set up auto-save if required.
$autosaveperiod = get_config('quiz', 'autosaveperiod');
if ($autosaveperiod) {
    $PAGE->requires->yui_module('moodle-mod_quiz-autosave',
            'M.mod_quiz.autosave.init', [$autosaveperiod]);
}

// Log this page view.
$attemptobj->fire_attempt_viewed_event();

// Get the list of questions needed by this page.
$slots = $attemptobj->get_slots($page);

// Check.
if (empty($slots)) {
    throw new moodle_exception('noquestionsfound', 'quiz', $attemptobj->view_url());
}

// Update attempt page, redirecting the user if $page is not valid.
if (!$attemptobj->set_currentpage($page)) {
    redirect($attemptobj->start_attempt_url(null, $attemptobj->get_currentpage()));
}

// Initialise the JavaScript.
$headtags = $attemptobj->get_html_head_contributions($page);
$PAGE->requires->js_init_call('M.mod_quiz.init_attempt_form', null, false, quiz_get_js_module());
\core\session\manager::keepalive(); // Try to prevent sessions expiring during quiz attempts.

// Arrange for the navigation to be displayed in the first region on the page.
$navbc = $attemptobj->get_navigation_panel($output, navigation_panel_attempt::class, $page);
$regions = $PAGE->blocks->get_regions();
$PAGE->blocks->add_fake_block($navbc, reset($regions));

$headtags = $attemptobj->get_html_head_contributions($page);
$PAGE->set_title($attemptobj->attempt_page_title($page));
$PAGE->add_body_class('limitedwidth');
$PAGE->set_heading($attemptobj->get_course()->fullname);
$PAGE->activityheader->disable();
if ($attemptobj->is_last_page($page)) {
    $nextpage = $page + 1;
} else {
    $nextpage = $page + 1;
}

$contentNav = $navbc->content;
$quantityAnswered = substr_count( $contentNav, 'answersaved' );
$quantityNotAnswered = substr_count( $contentNav, 'notyetanswered' );
$quantityMark = substr_count( $contentNav, 'flagged' );
$totalQuantity = $quantityAnswered + $quantityNotAnswered;

echo $output->attempt_page($attemptobj, $page, $accessmanager, $messages, $slots, $id, $nextpage);

$ssKey = sesskey();
$isLasPage = $attemptobj->is_last_page($page);

echo '<script>
    let hrefCancelAttemps = "";

    function nextQuestionRadioButton(){
        const isLast = "'. $isLasPage .'"
        const listRadioButton = document.getElementsByClassName("answer");
        const nextButton = document.getElementById("mod_quiz-next-nav");
        for (let radio of listRadioButton[0].children){
            const input = radio.children[0];
            input.addEventListener("change", function() {
                if(isLast !== "1"){
                    nextButton.click();
                } else {
                    nextButton.click();
                }
            });
        }
    }
    
    function createNewButtonCancel(){
        const jsonSure = ["areyousure"];
        const jsonDeleteCore = ["confirm","core"];
        const renderARef = document.createElement("a");
        const textARef = document.createTextNode("Cancelar intento");
        renderARef.setAttribute("type", "submit");
        renderARef.setAttribute("data-modal", "confirmation");
        renderARef.setAttribute("data-modal-title-str", JSON.stringify(jsonDeleteCore));
        renderARef.setAttribute("data-modal-content-str", JSON.stringify(jsonSure));
        renderARef.setAttribute("data-modal-yes-button-str", JSON.stringify(jsonDeleteCore));
        renderARef.setAttribute("data-modal-destination", hrefCancelAttemps);
        renderARef.appendChild(textARef);
        renderARef.id = "single_button";
        return renderARef;
    }
    
    function createForm(){
        const renderARef = document.createElement("form");
        renderARef.setAttribute("method","post");
        renderARef.setAttribute("action", "/mod/quiz/processattempt.php");
        renderARef.setAttribute("style","margin-left: auto;display: none;");
        return renderARef;
    }
    
    function createFakeForm(){
        const renderARef = document.createElement("button");
        const textARef = document.createTextNode("Terminar intento");
        renderARef.setAttribute("class", "mod_quiz-next-nav btn btn-primary");
        renderARef.setAttribute("type", "button");
        renderARef.setAttribute("onClick", "showModal()");
        renderARef.appendChild(textARef);
        renderARef.id = "single_button";
        return renderARef;
    }
    
    function reeplaceCancelButton(){
        const listNavQuestionarieButtons = document.getElementsByClassName("othernav");
        const listChildNavQuestionarie = listNavQuestionarieButtons[0].children;
        const cancelAttemps = listChildNavQuestionarie[0];
        hrefCancelAttemps = cancelAttemps.href;
        const newButton = createNewButtonCancel();
        cancelAttemps.style.display = "none";
        cancelAttemps.parentNode.insertBefore(newButton, cancelAttemps);
    }
    
    function hideNavegation(){
        const mainSection = document.getElementById("region-main");
        const navigationSection = mainSection.lastElementChild;
        navigationSection.style.display = "none";
    }
    
    function fixOrderNavQuestionarie(){
        const navQuestionarie = document.getElementsByClassName("qn_buttons clearfix multipages");
        navQuestionarie[0].style.gridGap = "0";
    }
    
    function insertFinishButton(nodeParent){
    const cmid = ' . $cmid . ';
    const ssKey = "' . $ssKey . '";
    const attempt = "'. $attemptid .'"
        const allHtml = "<input type=\'hidden\' name=\'attempt\' value=\'"+attempt+"\'><input type=\'hidden\' name=\'finishattempt\' value=\'1\'><input type=\'hidden\' name=\'timeup\' value=\'0\'><input type=\'hidden\' name=\'slots\' value=\'\'><input type=\'hidden\' name=\'cmid\' value=\'"+cmid+"\'><input type=\'hidden\' name=\'sesskey\' value=\'"+ssKey+"\' ><button type=\'submit\' class=\'btn btn-primary\' id=\'frm-finishattempt\'>Enviar todo y terminar</button>";
        nodeParent.insertAdjacentHTML("beforeend", allHtml);
    }
    
    function reeplaceButtonFinish(){
        const isLast = "'. $isLasPage .'"
        const newForm = createForm();
        const newFakeForm = createFakeForm();
        const newFakeFormRight = createFakeForm();
        const buttonFinish = document.getElementsByClassName("mod_quiz-next-nav btn btn-primary")[0];
        const buttonFinishRight = document.getElementsByClassName("endtestlink aalink")[0];
        buttonFinishRight.parentNode.insertBefore(newFakeFormRight, buttonFinishRight);
        isLast === "1" ? buttonFinish.parentNode.insertBefore(newFakeForm, buttonFinish.nextSibling): null;
        buttonFinish.parentNode.insertBefore(newForm, buttonFinish);
        insertFinishButton(newForm);
        isLast === "1" ? buttonFinish.style.display = "none" : null;
        buttonFinishRight.style.display = "none";
    }
    
    function showModal(){
        const modal = document.getElementById("modal_finish_all");
        const backdrop = document.getElementById("backdrop_modal_finish");
        modal.classList.add("show");
        backdrop.classList.add("show");
        modal.classList.remove("hide");
        backdrop.classList.remove("hide");
    }
    
    function hideModal(){
        const modal = document.getElementById("modal_finish_all");
        const backdrop = document.getElementById("backdrop_modal_finish");
        modal.classList.remove("show");
        backdrop.classList.remove("show");
        modal.classList.add("hide");
        backdrop.classList.add("hide");
    }
    
    function goToFormFinish(){
        const form = document.getElementById("frm-finishattempt");
        window.onbeforeunload = null;
        form.click();
    }
    
    nextQuestionRadioButton();
    reeplaceCancelButton();
    hideNavegation();
    fixOrderNavQuestionarie();
    reeplaceButtonFinish();
</script>

<style>
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
    #mod_quiz_navblock .answersaved .trafficlight{
        z-index: -1;
        background-color: #e6eaf7 !important;
    }
    #mod_quiz_navblock .answersaved{
        background-color: #e6eaf7 !important;
    }
    /* Extra small devices (phones, 600px and down) */
    @media only screen and (max-width: 600px) {
        .modal-content{
            margin-top: 24%;
        }
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
    .info div div a{
        width: 112px;
        background-color: rgb(254, 237, 237) !important;
        border: 1px solid transparent !important;
        color: rgb(145, 8, 8) !important;
    }
    .info div div a[aria-pressed="false"]{
        font-size: 0 !important;
    }
    .info div div a[aria-pressed="false"]:before{
        font-size: .8125rem;
        content:"Me arriesgo"; 
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
                    <h5 class="modal-title-special" data-region="title">Terminar intento</h5>
                    <button type="button" class="close"  onClick="hideModal()" data-action="hide" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body-special" data-region="body" style="">
                    <div class="quiz-submission-confirmation-content">
                        <div class="mb-3">
                            Una vez que haga el envío, no podrá cambiar sus respuestas de este intento de resolver el cuestionario..
                        </div>
                            <div class="alert alert-warning">
                                Preguntas respondidas: '. $quantityAnswered .'
                            </div>
                            <div class="alert alert-warning">
                                Preguntas sin responder: '. $quantityNotAnswered .'
                            </div>
                            <div class="alert alert-warning">
                                Preguntas con riesgos: '. $quantityMark .'
                            </div>
                            <div class="alert alert-warning">
                                Total de preguntas: '. $totalQuantity .'
                            </div>
                    </div>
                </div>
                <div class="modal-footer" data-region="footer">
    
        <div class="w-100 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" onClick="hideModal()" data-action="cancel">Cancelar</button>
            <button type="button" class="btn btn-primary" onClick="goToFormFinish()">Confirmar</button>
        </div>
                    </div>
            </div>
        </div>
    </div>
    <div tabindex="-1" style="position: fixed; top: 0px; left: 0px;" aria-hidden="true" data-aria-hidden-tab-index="0"></div>
</div>

';