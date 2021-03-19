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


require_once(__DIR__ . '/../../config.php');

global $DB, $USER;

$refurl = get_local_referer(false);
$indexurl = new moodle_url('/blocks/bookreport/index.php');
$table_myreportsurl = new moodle_url('/blocks/bookreport/table_myreports.php');
$table_allreportsurl = new moodle_url('/blocks/bookreport/table_allreports.php');
$create_streporturl = new moodle_url('/blocks/bookreport/create_streport.php');
$create_prsreporturl = new moodle_url('/blocks/bookreport/create_prsreport.php');
$libraryurl = new moodle_url('/course/index.php?categoryid=30');
$sendreporturl = new moodle_url('/blocks/bookreport/sendreport.php');

$PAGE->set_url($table_myreportsurl);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('myreports', 'block_bookreport'));
$PAGE->set_heading(get_string('myreports', 'block_bookreport'));

$PAGE->navbar->add(get_string('shortpluginname', 'block_bookreport'), $indexurl);
$PAGE->navbar->add(get_string('myreports', 'block_bookreport'));

$PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/bookreport/style/css/jquery-ui.css'));

$params = [    
    'allreports' => false,
    'userid' => $USER->id    
];

$PAGE->requires->js_call_amd('block_bookreport/dataTables_main', 'dtInit', $params);
$PAGE->requires->js_call_amd('block_bookreport/dataTables_main', 'dpInit');

$templatecontext = new stdClass;
$templatecontext->all_my_reports = get_string('bookreports', 'block_bookreport');
$templatecontext->streport = get_string('streport', 'block_bookreport');
$templatecontext->prsreport = get_string('prsreport', 'block_bookreport');
$templatecontext->indexurl = $indexurl;
$templatecontext->sendreporturl = $sendreporturl;
$templatecontext->table_myreportsurl = $table_myreportsurl;
$templatecontext->table_allreportsurl = $table_allreportsurl;
$templatecontext->create_streporturl = $create_streporturl;
$templatecontext->create_prsreporturl = $create_prsreporturl;
$templatecontext->libraryurl = $libraryurl;

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('block_bookreport/table_reports', $templatecontext);

echo $OUTPUT->footer();