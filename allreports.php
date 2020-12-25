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

global $DB;

$allreporturl = new moodle_url('/blocks/bookreport/allreports.php');
$indexurl = new moodle_url('/blocks/bookreport/index.php');
$PAGE->set_url($allreporturl);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Все отчеты');
$PAGE->set_heading(get_string('pluginname', 'block_bookreport'));

$navallreports = get_string('allreports', 'block_bookreport');
$navindex = get_string('bookreport', 'block_bookreport');
$PAGE->navbar->add($navindex, $indexurl);
$PAGE->navbar->add($navallreports, $allreporturl);

$params = [];

$sql =" SELECT bb.id, bb.type, bb.timecreated, bb.type, bb.user_id, bs.author, bs.book, u.firstname, u.lastname
        FROM {block_bookreport} bb
        JOIN {block_bookreport_strep} bs ON (bs.bookreportid = bb.id)
        JOIN {user} u ON (u.id = bb.user_id)       
";

$reports = $DB->get_records_sql($sql, $params);

$reportsstr = [];
foreach ($reports as $report) {
    $reportstr = $report->author . ' ' 
        .$report->book . '. Стандартный отчет. Дата создания: ' 
        .date('jS F Y h:i:s A (T)', $report->timecreated)  . ' ' 
        .$report->firstname . ' ' 
        .$report->lastname;
    array_push($reportsstr, $reportstr);
}
$reportsstr = array_values($reportsstr);

$templatecontext = [
    'reportsdata' => $reportsstr
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_bookreport/allreports', $templatecontext);
echo $OUTPUT->footer();