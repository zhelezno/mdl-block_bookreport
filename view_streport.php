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
 * Version information
 *
 * @package   block_bookreport
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


 /**
 * Page settings
 */
require_once(__DIR__ . '/../../config.php');

global $DB, $USER, $PAGE;

//Get optional params . Получение get параметров
$id = optional_param('id', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$refurl = get_local_referer(false);

//Urls
$indexurl = new moodle_url('/blocks/bookreport/index.php');
$table_myreportsurl = new moodle_url('/blocks/bookreport/table_myreports.php');
$table_allreportsurl = new moodle_url('/blocks/bookreport/table_allreports.php');
$create_streporturl = new moodle_url('/blocks/bookreport/create_streport.php');
$create_prsreporturl = new moodle_url('/blocks/bookreport/create_prsreport.php');
$view_streporturl = new moodle_url('/blocks/bookreport/view_streport.php');
$libraryurl = new moodle_url('/course/index.php?categoryid=30');
$sendreporturl = new moodle_url('/blocks/bookreport/sendreport.php');
$updatereporturl = new moodle_url('/blocks/bookreport/updatereport.php');

//Set page
$PAGE->set_url($view_streporturl);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('bookreport', 'block_bookreport'));
$PAGE->set_heading(get_string('pluginname', 'block_bookreport'));

//User is admin?
if((!is_siteadmin()) && ($USER->id != $userid)) {
    redirect($refurl, get_string('error_reportwronguser', 'block_bookreport'),null, \core\output\notification::NOTIFY_ERROR);
}

//Report exist?
$reportinfo = std_to_arr(get_report_info());
if (empty($reportinfo)) {
    redirect($refurl, get_string('error_reportdoesnotexist', 'block_bookreport'),null, \core\output\notification::NOTIFY_ERROR);
}

$PAGE->navbar->add(get_string('shortpluginname', 'block_bookreport'), $indexurl);
if(get_change_control($reportinfo['user_id']) === 'disabled') {
    $PAGE->navbar->add(get_string('allreports', 'block_bookreport'), $table_allreportsurl);
} else {
    $PAGE->navbar->add(get_string('myreports', 'block_bookreport'), $table_myreportsurl);
}
$PAGE->navbar->add(get_string('userreport', 'block_bookreport'));


/**
 * Main
 */
$report = std_to_arr(get_standart_report($reportinfo['id']));

$templatecontext = new stdClass;
$templatecontext->all_my_reports = get_string('allreports', 'block_bookreport');
$templatecontext->streport = get_string('streport', 'block_bookreport');
$templatecontext->prsreport = get_string('prsreport', 'block_bookreport');
$templatecontext->bookreport = get_string('bookreport', 'block_bookreport');
$templatecontext->indexurl = $indexurl;
$templatecontext->sendreporturl = $sendreporturl;
$templatecontext->table_myreportsurl = $table_myreportsurl;
$templatecontext->table_allreportsurl = $table_allreportsurl;
$templatecontext->create_streporturl = $create_streporturl;
$templatecontext->create_prsreporturl = $create_prsreporturl;
$templatecontext->libraryurl = $libraryurl;
$templatecontext->report = $report;
$templatecontext->changecontrol = get_change_control($userid);
$templatecontext->updatereporturl = $updatereporturl.'?id='.$id.'&userid='.$userid;

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('block_bookreport/view_streport', $templatecontext);

echo $OUTPUT->footer();


/**
 * 
 * 
 * 
 * Functions
 * 
 * 
 * 
 */
function get_standart_report($report_id) {

    global $DB;

    $params = [
        'report_id' => $report_id
    ];

    $sql =" SELECT bs.author, bs.book, bs.mainactors, bs.mainidea, bs.quotes, bs.conclusion
            FROM {block_bookreport_strep} bs            
            WHERE bs.bookreportid = :report_id          
    ";

    return $DB->get_records_sql($sql, $params);
}

function get_report_info() {

    global $DB;    

    $params = [
        'id' => $_GET['id'],
        'userid' => $_GET['userid']
    ];

    //Получение отчета
    $sql =" SELECT bb.id, bb.user_id, bb.type
            FROM {block_bookreport} bb            
            WHERE bb.id = :id
            AND bb.user_id = :userid
    ";

    return $DB->get_records_sql($sql, $params);
}

function std_to_arr($stdArr) {

    $defArr = json_decode(json_encode($stdArr), true);
    
    foreach ($defArr as $arr){ 
        return $arr;  
    }   
}

function get_change_control($userid){
    
    global $USER;

    //атрибут disabled для исключения возможности править записи другим пользователям
    $changecontrol = '';
    
    if ($userid !==  $USER->id) {
        $changecontrol = 'disabled'; 
    }

    return $changecontrol;
}