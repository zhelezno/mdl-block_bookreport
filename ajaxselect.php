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


define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php'); 

function fetch($userid){    
    global $DB;

    $params = [];
    $sql = "";

    $sql .= "   SELECT bs.author, bs.book, CONCAT(u.firstname, ' ', u.lastname) AS fullname, u.department, FROM_UNIXTIME(bb.timecreated) AS timecreated,  bb.type, bb.id, bb.user_id
                FROM {block_bookreport} bb
                JOIN {block_bookreport_strep} bs ON (bs.bookreportid = bb.id)
                JOIN {user} u ON (u.id = bb.user_id)   
        ";   

    if ($userid != null) {
        $params['userid'] = $userid;        
        $sql .= "   WHERE
                    u.id = :userid
                ";
    };
        
    return $DB->get_records_sql($sql, $params);
};

function date_range($userid, $start_date, $end_date){
    global $DB;     

    if (isset($start_date) && isset($end_date)) {

        $params = [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
        $sql = "";
        
        $sql .= "SELECT bs.author, bs.book, CONCAT(u.firstname, ' ', u.lastname) AS fullname, u.department, FROM_UNIXTIME(bb.timecreated) AS timecreated,  bb.type, bb.id, bb.user_id
                 FROM {block_bookreport} bb
                 JOIN {block_bookreport_strep} bs ON (bs.bookreportid = bb.id)
                 JOIN {user} u ON (u.id = bb.user_id)
                 WHERE bb.timecreated > :start_date
                 AND bb.timecreated < :end_date
        ";
        
        if ($userid != null) {
            $params['userid'] = $userid;                   
            $sql .= "   AND
                        u.id = :userid
                ";
        };   
       
    }
        
    return $DB->get_records_sql($sql, $params);
}

if (isset($_POST['userid'])) {
    $userid = $_POST['userid'];    
} else {
    $userid = null;
}

if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $rows = date_range($userid, $start_date, $end_date);    
} else {
    $rows = fetch($userid);    
}

$rows = array_values($rows);
echo json_encode($rows);