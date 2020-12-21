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
 * @package   local_bookreport
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');

global $DB;

$url = new moodle_url('/local/bookreport/index.php');
$PAGE->set_url($url);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Отчет по книге');

$toreviewstr = get_string('bookreport', 'pluginname');
$PAGE->navbar->add($toreviewstr, $url);

$PAGE->requires->js_call_amd('local_bookreport/selecttypereport', 'typereport');

$templatecontext = [];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_bookreport/index', $templatecontext);
echo $OUTPUT->footer();