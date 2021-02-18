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
 * @author    chasnikovandrew@gmail.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/bookreport/classes/form/filemanager.php');

global $DB, $USER;

$url = new moodle_url('/blocks/bookreport/filepickform.php');
$refurl = get_local_referer(false);
$PAGE->set_url($url);

$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Отчет по книге');
$PAGE->set_heading(get_string('pluginname', 'block_bookreport'));

$courseid = 1;
$context = context_course::instance($courseid);
$PAGE->set_context($context);

$myreporturl = new moodle_url('/blocks/bookreport/myreports.php');
$allreporturl = new moodle_url('/blocks/bookreport/allreports.php');

$navbookreport = get_string('bookreport', 'block_bookreport');
$PAGE->navbar->add($navbookreport, $url);

$PAGE->requires->js_call_amd('block_bookreport/insertForm_main', 'typereport');

$templatecontext = new stdClass;
$templatecontext->myreporturl = $myreporturl;
$templatecontext->allreporturl = $allreporturl;

$filemanageropts = array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1000, 'context' => $context);
$customdata = array('filemanageropts' => $filemanageropts);
$filepick_form = new filemanager(null, $customdata);

if ($form_submitted_data = $filepick_form->get_data()) {
    
    $filerecord = new stdClass;
    $filerecord->attachment = $form_submitted_data->attachment;
    $filerecord->contextid = $context->id;
    $filerecord->component =  'block_bookreport';
    $filerecord->filearea = 'item_file';
    $filerecord->options = array(
                                    'subdirs' => 0, 
                                    'maxbytes' => 0, 
                                    'maxfiles' => 1000
                                );
    
    file_save_draft_area_files(
        $filerecord->attachment, 
        $filerecord->contextid, 
        $filerecord->component,
        $filerecord->filearea,
        $filerecord->attachment, 
        $filerecord->options
    );    
    
    $reportinfo = new stdClass();
    $reportinfo->user_id = $USER->id;
    $reportinfo->type = 2;
    $reportinfo->completed = 1;
    $reportinfo->timecreated = time();  
    $reportinfo->timemodified = $reportinfo->timecreated; 
    
    $lastinsertid = $DB->insert_record('block_bookreport', $reportinfo, $returnid=true, $bulk=false);
   
    $form_submitted_data->bookreportid = $lastinsertid;
    $DB->insert_record('block_bookreport_prsrep', $form_submitted_data);
   
    redirect($refurl, get_string('indexreportredirect', 'block_bookreport'));
} else {
    
    $site = get_site();
    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('block_bookreport/presentationreport', $templatecontext);
    $filepick_form->display();
    echo $OUTPUT->footer();
}




















/*$mform = new filemanager();
echo $OUTPUT->header();

echo $OUTPUT->render_from_template('block_bookreport/presentationreport', $templatecontext);

$mform->display();

echo $OUTPUT->footer();*/