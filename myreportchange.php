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

$myreportchangeurl = new moodle_url('/blocks/bookreport/myreportchange.php');
$indexurl = new moodle_url('/blocks/bookreport/index.php');
$myreportsurl = new moodle_url('/blocks/bookreport/myreports.php');
$PAGE->set_url($myreportchangeurl);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Отчет');
$PAGE->set_heading(get_string('pluginname', 'block_bookreport'));

$navuserreport = get_string('userreport', 'block_bookreport');
$navmyreports = get_string('myreports', 'block_bookreport');
$navindex = get_string('bookreport', 'block_bookreport');
$PAGE->navbar->add($navindex, $indexurl);
$PAGE->navbar->add($navmyreports, $allreportsurl);
$PAGE->navbar->add($navuserreport, $userreporturl);

$id = $_GET['id'];
$userid = $_GET['userid'];

$params = [
    'id' => $id,
    'userid' => $userid
];

$sql =" SELECT bs.author, bs.book, bs.mainactors, bs.mainidea, bs.quotes, bs.conclusion
        FROM {block_bookreport_strep} bs
        JOIN {block_bookreport} bb ON (bb.id = bs.bookreportid)
        WHERE bb.id = :id
        AND bb.user_id = :userid
";

$report = $DB->get_records_sql($sql, $params);
$report = array_values($report);

$templatecontext = [
    'report' => $report
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_bookreport/myreportchange', $templatecontext);
echo $OUTPUT->footer();       