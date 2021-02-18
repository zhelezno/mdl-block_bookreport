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


 /**
 * Page settings
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/bookreport/classes/form/fileviewer.php');

global $CFG, $DB, $USER;

//Get optional params
$id = optional_param('id', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$refurl = get_local_referer(false);

//Urls
$indexurl = new moodle_url('/blocks/bookreport/index.php');
$myreportsurl = new moodle_url('/blocks/bookreport/myreports.php');
$allreportsurl = new moodle_url('/blocks/bookreport/allreports.php');
$myreporturl = new moodle_url('/blocks/bookreport/myreportchange.php');
$updatereporturl = new moodle_url('/blocks/bookreport/updatereport.php');

//Set page
$PAGE->set_url($myreporturl);
$PAGE->set_title('Отчет');
$PAGE->set_heading(get_string('pluginname', 'block_bookreport'));

//Only main page ..site/my/ plugin, therefore we will explicitly set the id course
$courseid = 1;
$context = context_course::instance($courseid);
$PAGE->set_context($context);

//Add navbar
$PAGE->navbar->add(get_string('bookreport', 'block_bookreport'), $indexurl);
$PAGE->navbar->add(get_string('allreports', 'block_bookreport'), $allreportsurl);
$PAGE->navbar->add(get_string('userreport', 'block_bookreport'));

//Initialize mform
$fileview_form = new fileviewer();

//If the form was submitted
if ($form_submitted_data = $fileview_form->get_data()) {
    //And bookreport id != 0
    if ($form_submitted_data->id != 0){
        //And report belongs to its creator
        if ($userid == $USER->id){
            
            //File settings
            $filerecord = new stdClass;
            $filerecord->attachment = $form_submitted_data->attachment; //Form attachment
            $filerecord->contextid = $context->id;
            $filerecord->component =  'block_bookreport';
            $filerecord->filearea = 'item_file';
            $filerecord->options = array(
                                            'subdirs' => 0, 
                                            'maxbytes' => 0,  
                                            'maxfiles' => 1000
                                        );
            
            //Update file into user draft area and custom item_file area
            if(!file_save_draft_area_files(
                $filerecord->attachment, 
                $filerecord->contextid, 
                $filerecord->component,
                $filerecord->filearea,
                $filerecord->attachment, 
                $filerecord->options
            )) {
                print_error('updateerror');
            }; 

            //Update main data into the custom table 
            if (!$DB->update_record('block_bookreport_prsrep', $form_submitted_data)) {
                print_error('updateerror');
            }
        } else {
            print_error('updateerror');
        }
    }
   
    redirect($refurl, get_string('viewreportredirect', 'block_bookreport'));
} else {
    
    //If the form is displayed for the first time
    $site = get_site();
    echo $OUTPUT->header();

    //Get record info
    $reportpage = $DB->get_record('block_bookreport_prsrep', array('bookreportid' => $id));

    //Set draftitemid(report attachment)
    $draftitemid = $reportpage->attachment;

    //If report belongs to its creator
    if (($userid == $USER->id) && ($id)){

        $fileview_form->set_data($reportpage);
        file_prepare_draft_area(
            $draftitemid, 
            $context->id, 
            'block_bookreport', 
            'item_file', 
            $draftitemid,
            array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1));
        
        $fileview_form->display();
    } elseif($id) {    
        
        //Get file storage
        $fs = get_file_storage();
        
        //If files were getting
        if ($files = $fs->get_area_files(
                $context->id, 
                'block_bookreport', 
                'item_file', 
                $draftitemid, 
                'sortorder', 
                false
            )){ 
                foreach ($files as $file) {
                    //Create report.pptx url
                    $fileurl = moodle_url::make_pluginfile_url(
                        $file->get_contextid(),
                        $file->get_component(),
                        $file->get_filearea(),
                        $file->get_itemid(),
                        $file->get_filepath(), 
                        $file->get_filename(),
                        true
                    );
                    $filename = $file->get_filename();
                }
        };

        //Get main report info
        $reportinfo = $DB->get_record('block_bookreport_prsrep', array('bookreportid' => $id), 'author,book');     

        //Set templatesettings
        $templatecontext = new stdClass();
        $templatecontext->fileurl = $fileurl;
        $templatecontext->filename = $filename;
        $templatecontext->author = $reportinfo->author;
        $templatecontext->book = $reportinfo->book;
       
        echo $OUTPUT->render_from_template('block_bookreport/viewpresentationreport', $templatecontext);
    }
    

    echo $OUTPUT->footer();
}
