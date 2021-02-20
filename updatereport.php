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

global $DB, $USER;

$refurl = get_local_referer(false);
$userid = required_param('userid', PARAM_INT);
$id = required_param('id', PARAM_INT);

if ($USER->id == $userid){

    $mountcreated = gmdate("m", check_date($id)->timecreated);
    $monthupdate = date('m');

    if ($mountcreated == $monthupdate) {

        $report = get_report($id);
        $DB->update_record('block_bookreport_strep', $report);
    
        redirect($refurl, get_string('viewreportredirect', 'block_bookreport'));
    } else {
        redirect($refurl, get_string('wrongmonth', 'block_bookreport'), null, \core\output\notification::NOTIFY_WARNING);
    }
   
} else {

    redirect($refurl, get_string('accessdenied', 'block_bookreport'), null, \core\output\notification::NOTIFY_WARNING);
}


/**
 * 
 * 
 * 
 * Func)
 * 
 * 
 * 
 */
function get_report($id){

    global $DB;

    $bsid = $DB->get_record('block_bookreport_strep', array('bookreportid' => $id), 'id'); 
    
    $stdreport = new stdClass;
    $stdreport->id = $bsid->id;
    $stdreport->mainactors = required_param('defaulttype_mainactors', PARAM_TEXT);
    $stdreport->mainidea = required_param('defaulttype_mainidea', PARAM_TEXT);
    $stdreport->quotes = required_param('defaulttype_quotes', PARAM_TEXT);
    $stdreport->conclusion = required_param('defaulttype_conclusion', PARAM_TEXT);      

    return $stdreport;    
}
function check_date($id){

    global $DB;

    $timecreated = $DB->get_record('block_bookreport', array('id' => $id), 'timecreated');

    return $timecreated;
}