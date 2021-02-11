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
require_once($CFG->dirroot . '/blocks/bookreport/classes/form/fileviewer.php');

$id = optional_param('id', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);

$indexurl = new moodle_url('/blocks/bookreport/index.php');
$myreportsurl = new moodle_url('/blocks/bookreport/myreports.php');
$allreportsurl = new moodle_url('/blocks/bookreport/allreports.php');
$myreporturl = new moodle_url('/blocks/bookreport/myreportchange.php');
$updatereporturl = new moodle_url('/blocks/bookreport/updatereport.php');

$PAGE->set_url($myreporturl);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Отчет');
$PAGE->set_heading(get_string('pluginname', 'block_bookreport'));

$PAGE->navbar->add(get_string('bookreport', 'block_bookreport'), $indexurl);
$PAGE->navbar->add(get_string('allreports', 'block_bookreport'), $allreportsurl);
$PAGE->navbar->add(get_string('userreport', 'block_bookreport'));

$fileview_form = new fileviewer();

if ($form_submitted_data = $fileview_form->get_data()) {
    
    file_save_draft_area_files($form_submitted_data->attachment, $context->id, 'block_bookreport', 'attachment',
        $form_submitted_data->attachment, array('subdirs' => 0, 'maxbytes' => 500000, 'maxfiles' => 1));

    if ($form_submitted_data->id != 0){
        if ($userid == $USER->id){
            if (!$DB->update_record('block_bookreport_prsrep', $form_submitted_data)) {
                print_error('updateerror');
            }
        } else {
            print_error('updateerror');
        }
    }
    //print_r($form_submitted_data);die;
    $refurl = get_local_referer(false);
    redirect($refurl, get_string('viewreportredirect', 'block_bookreport'));
} else {
    
    $site = get_site();
    echo $OUTPUT->header(); 
    if ($indexurl) {
        $reportpage = $DB->get_record('block_bookreport_prsrep', array('bookreportid' => $id));
        $fileview_form->set_data($reportpage);
        $draftitemid = $reportpage->attachment ; //file_get_submitted_draft_itemid('attachment');
        file_prepare_draft_area($draftitemid, $context->id, 'block_bookreport', 'attachment', $reportpage->attachment,
            array('subdirs' => 0, 'maxbytes' => 5000000, 'maxfiles' => 1));
    } 
    $fileview_form->display();
    echo $OUTPUT->footer();
}
