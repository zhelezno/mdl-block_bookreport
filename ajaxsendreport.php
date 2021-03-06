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

require_login();

$indexurl = new moodle_url('/blocks/bookreport/index.php');

//Main params
$reporttype = 1;
$completed = 0;
$timecreated = time();
$timemodified = $timecreated;
$report = getreport($completed);//Getting report from form/Получение полей отчета

if ((userhasdraft() == false)){//If the draft not exists, we create a recod/Если нет черновика, создаем запись

    create_newreport($reporttype, $completed, $timecreated, $timemodified ,$report);//Creating new draft report

} else {//Else update fields/Если есть, обновляем поля

    $reportinfo = userhasdraft();//Get user draft
    $id = $reportinfo['bsid'];
    $bookreportid = $reportinfo['bbid'];

    $record = new stdClass;
    $record->id = $id;
    $record->bookreportid = $bookreportid;
    $record->author = $report['author'];
    $record->book = $report['book'];
    $record->mainactors = $report['mainactors'];
    $record->mainidea = $report['mainidea'];
    $record->quotes = $report['quotes'];
    $record->conclusion = $report['conclusion'];

    $DB->update_record('block_bookreport_strep', $record);//Update draft
}


/**
 * Func
 */
function getreport(){

    $arrreport = [];    

    $arrreport['author'] = $_POST['author'];   
    $arrreport['book'] = $_POST['book'];
    $arrreport['mainactors'] = $_POST['mainactors'];
    $arrreport['mainidea'] = $_POST['mainidea'];
    $arrreport['quotes'] = $_POST['quotes'];
    $arrreport['conclusion'] = $_POST['conclusion'];        

    return $arrreport;    
}

function userhasdraft(){

    global $DB, $USER;

    $params = [
        'user_id' => $USER->id
    ];

    $sql = "";
    
    $sql .= "   SELECT bb.id as bbid, bs.id AS bsid    
                FROM {block_bookreport} bb
                JOIN {block_bookreport_strep} bs ON (bs.bookreportid = bb.id)
                WHERE bb.user_id = :user_id
                AND bb.completed <> 1
        "; 

    $result = $DB->get_records_sql($sql, $params);

    if (!empty($result)) {

        $result = json_decode(json_encode($result), true);
        $result = array_values($result);    
        
        $draft = [];
        $draft['bsid'] = $result[0]['bsid']; 
        $draft['bbid'] = $result[0]['bbid'];
        
        return $draft;
    } else {
        return false;
    }    
}

function create_newreport($reporttype, $completed, $timecreated, $timemodified, $report){

    global $USER, $DB;
    
    $params1 = [
        'userid' => $USER->id,
        'type' => $reporttype,
        'completed' => $completed,
        'timecreated' => $timecreated,
        'timemodified' => $timemodified    
    ];

    $params2 = [    
        'author' => $report['author'],
        'book' => $report['book'],
        'mainactors' => $report['mainactors'],
        'mainidea' => $report['mainidea'],
        'quotes' => $report['quotes'],
        'conclusion' => $report['conclusion']
    ];

    $sql1 = "INSERT INTO 
            mdl_block_bookreport(user_id, type, completed, timecreated, timemodified) 
            VALUES(:userid, :type, :completed, :timecreated, :timemodified)       
    ";

    $sql2 = "INSERT INTO
            mdl_block_bookreport_strep(bookreportid, author, book, mainactors, mainidea, quotes, conclusion)
            VALUES(LAST_INSERT_ID(), :author, :book, :mainactors, :mainidea, :quotes, :conclusion)     
    ";

    $DB->execute($sql1, $params1);
    $DB->execute($sql2, $params2);
}