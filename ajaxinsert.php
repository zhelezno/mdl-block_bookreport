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

global $DB, $USER;

$author = $_POST['author'];
$book = $_POST['book'];
$mainactors = $_POST['mainactors'];
$mainidea = $_POST['mainidea'];
$quotes = $_POST['quotes'];
$conclusion = $_POST['conclusion'];

//Проверяем, есть ли у пользователя черновик
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

//Если нет, создаем запись
if (empty($result)){
    $type = 1;
    $user = $USER->id;
    $completed = 0;
    $timecreated = time();
    $timemodified = time();    

    $params1 = [
        'userid' => $user,
        'type' => $type,
        'completed' => $completed,
        'timecreated' => $timecreated,
        'timemodified' => $timemodified    
    ];

    $params2 = [    
        'author' => $author,
        'book' => $book,
        'mainactors' => $mainactors,
        'mainidea' => $mainidea,
        'quotes' => $quotes,
        'conclusion' => $conclusion
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
} else { //если есть, обновляем поля
    $result = json_decode(json_encode($result), true);
    $result = array_values($result);    
    $id = $result[0]['bsid'];   
    $bookreportid = $result[0]['bbid'];    

    $record = new stdClass;
    $record->id = $id;
    $record->bookreportid = $bookreportid;
    $record->author = $author;
    $record->book = $book;
    $record->mainactors = $mainactors;
    $record->mainidea = $mainidea;
    $record->quotes = $quotes;
    $record->conclusion = $conclusion;    

    $DB->update_record('block_bookreport_strep', $record);  
}