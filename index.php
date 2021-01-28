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
require_once($CFG->dirroot . '/blocks/bookreport/classes/file/filemanager.php');

global $DB, $USER;

$url = new moodle_url('/blocks/bookreport/index.php');
$PAGE->set_url($url);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Отчет по книге');
$PAGE->set_heading(get_string('pluginname', 'block_bookreport'));

$navbookreport = get_string('bookreport', 'block_bookreport');
$PAGE->navbar->add($navbookreport, $url);

$myreporturl = new moodle_url('/blocks/bookreport/myreports.php');
$allreporturl = new moodle_url('/blocks/bookreport/allreports.php');
$sendreporturl = new moodle_url('/blocks/bookreport/sendreport.php');

$templatecontext = [
    'myreporturl' => $myreporturl,
    'allreporturl' => $allreporturl,
    'sendreporturl' => $sendreporturl    
];

//Если у пользователя есть сохраненный черновик, передать поля в шаблон
$params = [
    'user_id' => $USER->id
];
$sql = "";

$sql .= "   SELECT bs.author, bs.book, bs.mainactors, bs.mainidea, bs.quotes, bs.conclusion
            FROM {block_bookreport_strep} bs
            JOIN {block_bookreport} bb ON (bb.id = bs.bookreportid)
            WHERE bb.user_id = :user_id
            AND bb.completed != 1
    "; 
$autosavedreport = $DB->get_records_sql($sql, $params);
$amdcontext = [];
if (!empty($autosavedreport)) {            
    $stdreport = json_decode(json_encode($autosavedreport), true);   
    $report = array_values($stdreport);   
    $amdcontext = $report[0];
}

$PAGE->requires->js_call_amd('block_bookreport/insertForm_main', 'typereport', $amdcontext);
$PAGE->requires->js_call_amd('block_bookreport/insertForm_main', 'ajax_call_db');  

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('block_bookreport/index', $templatecontext);

echo $OUTPUT->footer();