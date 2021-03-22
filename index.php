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

global $DB, $USER, $PAGE;

//Page settings
$url = new moodle_url('/blocks/bookreport/index.php');
$table_myreportsurl = new moodle_url('/blocks/bookreport/table_myreports.php');
$table_allreportsurl = new moodle_url('/blocks/bookreport/table_allreports.php');
$create_streporturl = new moodle_url('/blocks/bookreport/create_streport.php');
$create_prsreporturl = new moodle_url('/blocks/bookreport/create_prsreport.php');
$libraryurl = new moodle_url('/course/index.php?categoryid=30');

$PAGE->set_url($url);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('shortpluginname', 'block_bookreport'));
$PAGE->set_heading(get_string('mainpage', 'block_bookreport'));
$PAGE->navbar->add(get_string('shortpluginname', 'block_bookreport'), $url);
$PAGE->requires->js_call_amd('block_bookreport/insertForm_main', 'typereport');
$PAGE->requires->js_call_amd('block_bookreport/insertForm_main', 'ajax_call_db');
$PAGE->requires->js_call_amd('block_bookreport/insertForm_main', 'ajax_call_booksearch_st');

$templatecontext = new stdClass;
$templatecontext->indexurl = $url;
$templatecontext->table_myreportsurl = $table_myreportsurl;
$templatecontext->table_allreportsurl = $table_allreportsurl;
$templatecontext->create_streporturl = $create_streporturl;
$templatecontext->create_prsreporturl = $create_prsreporturl;
$templatecontext->libraryurl = $libraryurl;
$templatecontext->adminmail = get_string('adminmail', 'block_bookreport');

//Main
$params = [
    'userid' => $USER->id,
    'userid_2' => $USER->id
];
$sql = "SELECT bb.id, bb.type, bb.timecreated, bb.type, bs.author, bs.book
        FROM {block_bookreport} bb
        JOIN {block_bookreport_strep} bs ON (bs.bookreportid = bb.id) 
        WHERE bb.user_id = :userid
        AND bb.completed = 1
        
        UNION ALL
        
        SELECT bb.id, bb.type, bb.timecreated, bb.type, br.author, br.book
        FROM {block_bookreport} bb
        JOIN {block_bookreport_prsrep} br ON (br.bookreportid = bb.id) 
        WHERE bb.user_id = :userid_2
        AND bb.completed = 1
        
        ORDER BY timecreated DESC LIMIT 10
        ";

$myreports = $DB->get_records_sql($sql, $params);

if (empty($myreports)) {
    $templatecontext->empty = '...';
} else {
    foreach ($myreports as $myreport){
        if ($myreport->type == 1){
            $myreport->pixurl = '/blocks/bookreport/style/img/reportpix1.png';
            $myreport->reporturl = '/blocks/bookreport/view_streport.php?id='. $myreport->id .'&userid=' . $USER->id;
        } else {
            $myreport->pixurl = '/blocks/bookreport/style/img/reportpix2.png';
            $myreport->reporturl = '/blocks/bookreport/view_prsreport.php?id='. $myreport->id .'&userid=' . $USER->id;
        }
    }
    $templatecontext->myreports = array_values($myreports);
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_bookreport/index', $templatecontext);
echo $OUTPUT->footer();