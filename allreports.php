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

global $DB, $PAGE;

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

/**
 *  Подключение DataTables Js через cdn
 */
$PAGE->requires->jquery();

//DataTables css
$PAGE->requires->css(new moodle_url('https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.23/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/r-2.2.7/sc-2.0.3/sp-1.2.2/datatables.min.css'));
//Datepicker css
$PAGE->requires->css(new moodle_url('https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'));

//DateTables js
$PAGE->requires->js(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js'), true);
$PAGE->requires->js(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js'), true);
$PAGE->requires->js(new moodle_url('https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.23/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/r-2.2.7/sc-2.0.3/sp-1.2.2/datatables.min.js'), true);
//Popper js
$PAGE->requires->js(new moodle_url('https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js'), true);
//Font awesome js
$PAGE->requires->js(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js'), true);
//Datepicker js(jq ui)
$PAGE->requires->js(new moodle_url('https://code.jquery.com/ui/1.12.1/jquery-ui.js'), true);

//DataTables init/settings js
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/blocks/bookreport/js/datatablesInit.js'));

//Datepicker init js
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/blocks/bookreport/js/datepickerInit.js'));

/**
 * 
 * Рендер шаблона
 */

$firstdaymonth = date_create('first day of last month')->format('Y-m-d');
$lastdaymonth =  date_create('last day of last month')->format('Y-m-d');

$templatecontext = [
    'firstdaymonth' => $firstdaymonth,
    'lastdaymonth' => $lastdaymonth
];



echo $OUTPUT->header();

echo $OUTPUT->render_from_template('block_bookreport/allreports', $templatecontext);

echo $OUTPUT->footer();