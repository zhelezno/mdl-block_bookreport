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

$indexurl = new moodle_url('/blocks/bookreport/index.php');

global $DB,$USER;

$type = 1;//////////

$user = $USER->id;
$completed = 1;
$timecreated = time();
$timemodified = time();

$author = $_POST['defaulttype_author'];
$book = $_POST['defaulttype_book'];
$mainactors = $_POST['defaulttype_mainactors'];
$mainidea = $_POST['defaulttype_mainidea'];
$quotes = $_POST['defaulttype_quotes'];
$conclusion = $_POST['defaulttype_conclusion'];


//Проверяем, есть ли у пользователя черновик
$params = [
    'user_id' => $USER->id
];
$sql = "";

$sql .= "   SELECT bb.id as bbid, bs.id AS bsid    
            FROM {block_bookreport} bb
            JOIN {block_bookreport_strep} bs ON (bs.bookreportid = bb.id)
            WHERE bb.user_id = :user_id
            AND bb.completed != 1
    "; 
$result = $DB->get_records_sql($sql, $params);

//Если есть, записываем отчет в уже существующую запись в бд
if(!empty($result)) {    
    $result = json_decode(json_encode($result), true);//Конвертируем std to arr
    $result = array_values($result);//Сбрасываем индексы
    $id = $result[0]['bsid'];//id bookreport_strep
    $bookreportid = $result[0]['bbid'];// id bookreport
    
    //Обновляем поля черновика
    $recordstrep = new stdClass;
    $recordstrep->id = $id;
    $recordstrep->bookreportid = $bookreportid;
    $recordstrep->author = $author;
    $recordstrep->book = $book;
    $recordstrep->mainactors = $mainactors;
    $recordstrep->mainidea = $mainidea;
    $recordstrep->quotes = $quotes;
    $recordstrep->conclusion = $conclusion;
    $DB->update_record('block_bookreport_strep', $recordstrep);     
    
    //Меняем значение completed на 1, выводим запись из статуса черновика
    $recordrep = new stdClass;
    $recordrep->id = $bookreportid;
    $recordrep->completed = 1;
    $DB->update_record('block_bookreport', $recordrep); 
    
    redirect($indexurl, 'Отчет создан!');
} else {

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

    redirect($indexurl, 'Отчет создан!');
}