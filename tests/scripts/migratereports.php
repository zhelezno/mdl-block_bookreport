<?php
/**
 * 
 * 
 * 
 * 
 * 
 * 
 * Этот скрипт получает отчеты из старых источников и записывает их в бд плагина
 * 
 * 
 * 
 * 
 * 
 * 
 */

//defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../config.php'); 

global $DB;

/**
 * 
 * Получение и запись в бд блока старых отчетов по прочитанным книгам, доступным на портале
 * 
 */

//Получение и запись в массив объектов
$params = [];    

$sql = "SELECT 
        feedback_completed.id, 
        feedback_completed.userid, 
        feedback_completed.timemodified AS timecreated, 
        SUBSTRING_INDEX(REPLACE(course.fullname, 'Книга: ', ''), '-', 1) AS author,
        RIGHT(course.fullname, (CHAR_LENGTH(course.fullname) - INSTR(course.fullname, '-'))) AS book
        FROM 
        {feedback_completed} feedback_completed
        JOIN {feedback} feedback ON (feedback.id = feedback_completed.feedback)
        JOIN {course} course ON (course.id = feedback.course)
        JOIN {course_categories} course_categories ON (course_categories.id = course.category)
        WHERE
        feedback.name = 'Отчет о прочтении книги'
        AND
        course_categories.parent = 30
        ";

$reports = $DB->get_records_sql($sql, $params); 
//print_r($reports); die;
foreach ($reports as $report){
    
    $sql = "SELECT
            feedback_value.value
            FROM 
            {feedback_value} feedback_value
            WHERE feedback_value.completed = :id            
            ";
    
    $values = $DB->get_records_sql($sql, ['id' => $report->id]); 
    $values = array_values($values);    
    $report->mainactors = $values[0]->value;
    $report->mainidea = $values[1]->value;
    $report->quotes = $values[2]->value;
    $report->conclusion = $values[3]->value . '/' . $values[4]->value . '/' . $values[5]->value;
}
//print_r($reports); die;

//Запись в бд блока
foreach ($reports as $report){

    $params1 = [
        'userid' => $report->userid,
        'type' => 1,
        'completed' => 1,
        'timecreated' => $report->timecreated,
        'timemodified' => $report->timecreated    
    ];

    $params2 = [    
        'author' => $report->author,
        'book' => $report->book,
        'mainactors' => $report->mainactors,
        'mainidea' => $report->mainidea,
        'quotes' => $report->quotes,
        'conclusion' => $report->conclusion
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

redirect('/blocks/bookreport/allreports.php', 'Скрипт номер 228 успешно сработал, удачи с БД, лол');