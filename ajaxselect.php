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

$userid = required_param('userid', PARAM_INT);
$start_date = required_param('start_date', PARAM_INT);
$end_date = required_param('end_date', PARAM_INT);

if (($start_date != 0) && ($end_date != 0)) {
    $rows = date_range($start_date, $end_date, $userid);    
} else {
    $rows = fetch($userid);    
}

$rows = array_values($rows);
echo json_encode($rows);


/**
 * Func
 */

function fetch($userid){    
    global $DB;

    $params = [];
    $sql = "";

    $sql .= "   SELECT bs.id, bs.author, bs.book, CONCAT(u.firstname, ' ', u.lastname) AS fullname, u.department, FROM_UNIXTIME(bb.timecreated) AS timecreated,  bb.type, bb.id, bb.user_id
                FROM {block_bookreport} bb
                JOIN {block_bookreport_strep} bs ON (bs.bookreportid = bb.id)
                JOIN {user} u ON (u.id = bb.user_id)
                WHERE bb.completed = 1
        ";   

    if ($userid != 0) {
        $params['userid'] = $userid;        
        $sql .= "   AND
                    u.id = :userid
                ";
    };

    $sql .="    UNION ALL

                SELECT bp.id, bp.author, bp.book, CONCAT(u.firstname, ' ', u.lastname) AS fullname, u.department, FROM_UNIXTIME(bb.timecreated) AS timecreated,  bb.type, bb.id, bb.user_id
                FROM {block_bookreport} bb
                JOIN {block_bookreport_prsrep} bp ON (bp.bookreportid = bb.id)
                JOIN {user} u ON (u.id = bb.user_id)
                ";

    if ($userid != 0) { 
        $params['userid_p'] = $userid;        
        $sql .= "   WHERE
                    u.id = :userid_p
                ";
    };
        
    return $reports = $DB->get_records_sql($sql, $params);
};

function date_range($start_date, $end_date, $userid){
    global $DB;         

        $params = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'start_date_p' => $start_date,
            'end_date_p' => $end_date
        ];
        $sql = "";
        
        $sql .= "SELECT bs.id AS bsid, bs.author, bs.book, CONCAT(u.firstname, ' ', u.lastname) AS fullname, u.department, FROM_UNIXTIME(bb.timecreated) AS timecreated,  bb.type, bb.id, bb.user_id
                 FROM {block_bookreport} bb
                 JOIN {block_bookreport_strep} bs ON (bs.bookreportid = bb.id)
                 JOIN {user} u ON (u.id = bb.user_id)
                 WHERE bb.timecreated > :start_date
                 AND bb.timecreated < :end_date
                 AND bb.completed = 1
        ";
        
        if ($userid != 0) {
            $params['userid'] = $userid;                   
            $sql .= "   AND
                        u.id = :userid
                ";
        }; 
        
        $sql .="UNION ALL

                SELECT bp.id AS bpid, bp.author, bp.book, CONCAT(u.firstname, ' ', u.lastname) AS fullname, u.department, FROM_UNIXTIME(bb.timecreated) AS timecreated,  bb.type, bb.id, bb.user_id
                FROM {block_bookreport} bb
                JOIN {block_bookreport_prsrep} bp ON (bp.bookreportid = bb.id)
                JOIN {user} u ON (u.id = bb.user_id)
                WHERE bb.timecreated > :start_date_p
                AND bb.timecreated < :end_date_p                
                ";

    if ($userid != 0) {   
        $params['userid_p'] = $userid;     
        $sql .= "   AND
                    u.id = :userid_p
                ";
    };
        
    return $DB->get_records_sql($sql, $params);
}