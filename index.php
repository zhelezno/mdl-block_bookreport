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

$url = new moodle_url('/block/bookreport/index.php');
$PAGE->set_url($url);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Отчет по книге');
$PAGE->set_heading(get_string('pluginname', 'block_bookreport'));

$navbookreport = get_string('bookreport', 'block_bookreport');
$PAGE->navbar->add($navbookreport, $url);

$PAGE->requires->js_call_amd('block_bookreport/selecttypereport', 'typereport');

$myreporturl = new moodle_url('/blocks/bookreport/myreports.php');
$allreporturl = new moodle_url('/blocks/bookreport/allreports.php');
$insertreporturl = new moodle_url('/blocks/bookreport/insertreport.php');
$templatecontext = [
    'myreporturl' => $myreporturl,
    'allreporturl' => $allreporturl,
    'insertreporturl' => $insertreporturl
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_bookreport/index', $templatecontext);
echo $OUTPUT->footer();