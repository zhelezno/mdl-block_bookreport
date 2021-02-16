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
$refurl = get_local_referer(false);

$indexurl = new moodle_url('/blocks/bookreport/index.php');
$myreportsurl = new moodle_url('/blocks/bookreport/myreports.php');
$allreportsurl = new moodle_url('/blocks/bookreport/allreports.php');
$myreporturl = new moodle_url('/blocks/bookreport/myreportchange.php');
$updatereporturl = new moodle_url('/blocks/bookreport/updatereport.php');

$PAGE->set_url($myreporturl);
//$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Отчет');
$PAGE->set_heading(get_string('pluginname', 'block_bookreport'));

$PAGE->navbar->add(get_string('bookreport', 'block_bookreport'), $indexurl);
$PAGE->navbar->add(get_string('allreports', 'block_bookreport'), $allreportsurl);
$PAGE->navbar->add(get_string('userreport', 'block_bookreport'));

$courseid = 1;//contex = 1; context->id = 2 for moodle/my
$context = context_course::instance($courseid);
$contextid = $context->id;

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
   
    redirect($refurl, get_string('viewreportredirect', 'block_bookreport'));
} else {
    
    $site = get_site();
    echo $OUTPUT->header();

    $reportpage = $DB->get_record('block_bookreport_prsrep', array('bookreportid' => $id));
    $draftitemid = $reportpage->attachment;
    $fs = get_file_storage();
    if ($files = $fs->get_area_files($contextid, 'block_bookreport', 'attachment', $draftitemid, 'sortorder', false)){
        foreach ($files as $file) {
            $fileurl = moodle_url::make_pluginfile_url(
                $file->get_contextid(), 
                $file->get_component(), 
                $file->get_filearea(), 
                $file->get_itemid(), 
                $file->get_filepath(), 
                $file->get_filename(),
                true
            );

            $filelink =  '<a href="' . $fileurl . '" class="btn btn-outline-info"><img style="margin-right: 10px;" width="30px" src="../bookreport/style/img/downloadicon.png">' . $file->get_filename() . '</a>';
        }
    };

    if ($id) {
        $fileview_form->set_data($reportpage);
        file_prepare_draft_area($draftitemid, $context->id, 'block_bookreport', 'attachment', $draftitemid,
            array('subdirs' => 0, 'maxbytes' => 5000000, 'maxfiles' => 1));            
    } 
    $fileview_form->display();
    echo $filelink;
    echo $OUTPUT->footer();
}
