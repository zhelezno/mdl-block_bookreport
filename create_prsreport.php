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

global $DB, $USER, $PAGE;

//Page settings
$refurl = get_local_referer(false);
$indexurl = new moodle_url('/blocks/bookreport/index.php');
$table_myreportsurl = new moodle_url('/blocks/bookreport/table_myreports.php');
$table_allreportsurl = new moodle_url('/blocks/bookreport/table_allreports.php');
$create_streporturl = new moodle_url('/blocks/bookreport/create_streport.php');
$create_prsreporturl = new moodle_url('/blocks/bookreport/create_prsreport.php');
$libraryurl = new moodle_url('/course/index.php?categoryid=30');
$sendreporturl = new moodle_url('/blocks/bookreport/sendreport.php');

$PAGE->set_url($create_prsreporturl);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Отчет по книге');
$PAGE->set_heading(get_string('prsreport', 'block_bookreport'));

$courseid = 1;
$context = context_course::instance($courseid);
$PAGE->set_context($context);

$PAGE->navbar->add(get_string('shortpluginname', 'block_bookreport'), $indexurl);
$PAGE->navbar->add(get_string('prsreport', 'block_bookreport'));

$PAGE->requires->js_call_amd('block_bookreport/insertForm_main', 'ajax_call_booksearch_pr');

require_login();

//Mustache context
$templatecontext = new stdClass;
$templatecontext->indexurl = $indexurl;
$templatecontext->sendreporturl = $sendreporturl;
$templatecontext->streport = get_string('streport', 'block_bookreport');
$templatecontext->prsreport = get_string('prsreport', 'block_bookreport');
$templatecontext->table_myreportsurl = $table_myreportsurl;
$templatecontext->table_allreportsurl = $table_allreportsurl;
$templatecontext->create_streporturl = $create_streporturl;
$templatecontext->create_prsreporturl = $create_prsreporturl;
$templatecontext->libraryurl = $libraryurl;

//Filemanager settings
$filemanageropts = array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1000, 'context' => $context);
$customdata = array('filemanageropts' => $filemanageropts);
$filepick_form = new filemanager(null, $customdata);

if ($form_submitted_data = $filepick_form->get_data()) {//If the form was submitted

    if (check_resub_report($form_submitted_data->author, $form_submitted_data->book) == false){//If this is a unique user report/Если отчет уникальный
    
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
        redirect($refurl, get_string('error_resubmissionreport', 'block_bookreport'));
    }
} else {//If the form displayed first time(not submitted)
    $site = get_site();
    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('block_bookreport/create_prsreport', $templatecontext);
    $filepick_form->display();
    echo $OUTPUT->footer();
}



/**
 * Func
 */
function check_resub_report($author, $book){
    
    global $DB, $USER;

    $sql = "";
    $params = [
        'author' => trim($author),
        'book' => trim($book),
        'author2' => trim($author),
        'book2' => trim($book),
        'userid1' => $USER->id,
        'userid2' => $USER->id
    ];
    
    $sql .="SELECT
            bs.author AS author, bs.book AS book
            FROM {block_bookreport} AS bb
            JOIN {block_bookreport_strep} AS bs ON (bs.bookreportid = bb.id)
            WHERE
            bb.completed != 0
            AND
            bs.author LIKE CONCAT('%', :author, '%')
            AND
            bs.book LIKE CONCAT('%', :book, '%')
            AND 
            bb.user_id = :userid1
            
            UNION ALL

            SELECT
            br.author AS author, br.book AS book
            FROM {block_bookreport} AS bb
            JOIN {block_bookreport_prsrep} AS br ON (br.bookreportid = bb.id)
            WHERE
            bb.completed != 0
            AND
            br.author LIKE CONCAT('%', :author2, '%')
            AND
            br.book LIKE CONCAT('%', :book2, '%')
            AND 
            bb.user_id = :userid2
            ";

    if (!empty($DB->get_records_sql($sql, $params))){
        return true;
    };

    return false;
}