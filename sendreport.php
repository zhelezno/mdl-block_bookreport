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

$refurl = get_local_referer(false);
$redirectmessage = get_string('indexreportredirect', 'block_bookreport');

$reporttype = 1;
$completed = 1;
$timecreated = time();
$timemodified = $timecreated;
$report = getreport();//Getting report/Получение полей отчета

if (check_resub_report($report) == true){

    if ((userhasdraft() == false)){//If draft is empty we create a report/Если нет черновика, создаем запись

        create_newreport($reporttype, $completed, $timecreated, $timemodified, $report);
        
        $bookname = $report['book'] . ' ' . $report['author'];

        sendmsg($USER->id, $bookname);

        redirect($refurl, $redirectmessage);

    } else {//If user has draf we update the fields/Если есть, обновляем поля
        
        $reportinfo = userhasdraft();
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

        $DB->update_record('block_bookreport_strep', $record);

        $recordrep = new stdClass;
        $recordrep->id = $bookreportid;
        $recordrep->completed = 1;
        $recordrep->timecreated = $timecreated;
        $recordrep->timemodified = $timemodified;    
        $DB->update_record('block_bookreport', $recordrep);

        $bookname = $record->book . ' ' . $record->author;

        sendmsg($USER->id, $bookname);

        redirect($refurl, $redirectmessage);    
    }
} else {
    redirect($refurl, get_string('error_resubmissionreport', 'block_bookreport'));    
}


/**
 * Func
 */
function getreport(){

    $arrreport = [];   

    $arrreport['author'] = $_POST['defaulttype_author'];
    $arrreport['book'] = $_POST['defaulttype_book'];
    $arrreport['mainactors'] = $_POST['defaulttype_mainactors'];
    $arrreport['mainidea'] = $_POST['defaulttype_mainidea'];
    $arrreport['quotes'] = $_POST['defaulttype_quotes'];
    $arrreport['conclusion'] = $_POST['defaulttype_conclusion'];        

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
            {block_bookreport}(user_id, type, completed, timecreated, timemodified) 
            VALUES(:userid, :type, :completed, :timecreated, :timemodified)       
    ";

    $sql2 = "INSERT INTO
            {block_bookreport_strep}(bookreportid, author, book, mainactors, mainidea, quotes, conclusion)
            VALUES(LAST_INSERT_ID(), :author, :book, :mainactors, :mainidea, :quotes, :conclusion)     
    ";

    $DB->execute($sql1, $params1);
    $DB->execute($sql2, $params2);
}

function check_resub_report($report){
    
    global $DB, $USER;

    $sql = "";
    $params = [
        'author' => trim($report['author']),
        'book' => trim($report['book']),
        'author2' => trim($report['author']),
        'book2' => trim($report['book']),
        'userid1' => $USER->id,
        'userid2' => $USER->id
    ];
    
    $sql .="SELECT
            bs.author AS author, bs.book AS book
            FROM {block_bookreport} AS bb
            JOIN {block_bookreport_strep} AS bs ON (bs.bookreportid = bb.id)
            WHERE            
            bb.completed != 0
            AND
            bb.user_id = :userid1
            AND
            bs.author LIKE CONCAT('%', :author, '%')
            AND
            bs.book LIKE CONCAT('%', :book, '%')
            
            UNION ALL

            SELECT
            br.author AS author, br.book AS book
            FROM {block_bookreport} AS bb
            JOIN {block_bookreport_prsrep} AS br ON (br.bookreportid = bb.id)
            WHERE            
            bb.completed != 0
            AND
            bb.user_id = :userid2
            AND
            br.author LIKE CONCAT('%', :author2, '%')
            AND
            br.book LIKE CONCAT('%', :book2, '%')
            ";


    if (!empty($DB->get_records_sql($sql, $params))){
        return false;
    };

    return true;
}

function sendmsg($userid, $bookname) {

    $message = new \core\message\message();
    $message->component = 'moodle';
    $message->name = 'instantmessage';
    $message->userfrom = \core_user::get_noreply_user();
    $message->userto = $userid;
    $message->subject = 'Отчет по книге!';

    $message->fullmessageformat = FORMAT_MARKDOWN;

    $content = array(
        '*' => array(
            'header' => '<h4>Ваш отчет по книге ' . $bookname . ' принят!</h4>', 
            'footer' => '')
        ); 
    $message->set_additional_content('email', $content);
    

    $message->fullmessagehtml = '<a></a>';

    message_send($message);  
}