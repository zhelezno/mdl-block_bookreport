<?php
  
require_once(__DIR__ . '/../../../../config.php'); 

global $DB;

/** 
 * Получение старых отчетов по книгам, доступным на портале-----------------------------------------------------------------
 */
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

$reports = $DB->get_records_sql($sql, array()); 

/** 
 * Получение старых отчетов по книгам, доступным на портале(старый отчет)---------------------------------------------------
 */
$sql = "SELECT DISTINCT
        feedback_completed.id,
        user_info.id AS userid,
        feedback_completed.timemodified AS timecreted,
        feedback_value.value AS 'author',
        feedback_value2.value AS 'book'

        FROM 
        mdl_feedback_value AS feedback_value

        JOIN mdl_feedback_completed AS feedback_completed ON (feedback_completed.id = feedback_value.completed)
        JOIN mdl_user AS user_info ON (user_info.id = feedback_completed.userid)	
        JOIN mdl_feedback_value AS feedback_value2 ON (feedback_value2.completed = feedback_completed.id)

        WHERE
        feedback_completed.feedback = 1067
        AND
        feedback_value.item = 6280
        AND
        feedback_value2.item = 6281
        ";

$reports = array_merge($reports, $DB->get_records_sql($sql, array()));
//print_r($reports); die;

/** 
 * Получение отчетов по прочитанным личным книгам(старый отчет)
 */
$sql = "SELECT DISTINCT
        feedback_completed.id,
        user_info.id AS userid,
        feedback_completed.timemodified AS timecreated, 
        feedback_value.value AS author,
        feedback_value2.value AS book

        FROM 
        mdl_feedback_value AS feedback_value

        JOIN mdl_feedback_completed AS feedback_completed ON (feedback_completed.id = feedback_value.completed)
        JOIN mdl_user AS user_info ON (user_info.id = feedback_completed.userid)
        JOIN mdl_feedback_value AS feedback_value2 ON (feedback_value2.completed = feedback_completed.id)

        WHERE
        feedback_completed.feedback = 1073
        AND
        feedback_value.item = 6304
        AND
        feedback_value2.item = 6305
        ";
$reports = array_merge($reports, $DB->get_records_sql($sql, array()));
//print_r($reports); die;

/**
  * Получение отчетов по прочитанным личным книгам(тут используется мод questionnaire)
  */
$sql = "SELECT DISTINCT
        response.id,
        user_info.id AS userid,
        response.submitted AS timecreated,
        response_text.response AS author,
        response_text2.response AS book

        FROM 
        mdl_questionnaire_response_text AS response_text

        JOIN mdl_questionnaire_response AS response ON (response.id = response_text.response_id)
        JOIN mdl_user AS user_info ON (user_info.id = response.userid)
        JOIN mdl_questionnaire_response_text AS response_text2 ON (response_text2.response_id = response_text.response_id)
        JOIN mdl_questionnaire_question AS question ON (question.id = response_text.question_id)

        WHERE
        response_text.question_id = 1
        AND
        response_text2.question_id = 2
        ";
$questionnairereports = $DB->get_records_sql($sql, array()); 
//print_r($questionnairereports); die;

/**
 * Получение полей отчетов для отчетов, записанных в таблицы feedback
 */
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
//print_r(count($reports)); die;

/**
 * Получение полей отчетов для отчетов, записанных в таблицы questionnaire
 */
foreach ($questionnairereports as $report){
    
    $sql = "SELECT
            questionnaire_response_text.response
            FROM 
            {questionnaire_response_text} questionnaire_response_text
            WHERE questionnaire_response_text.response_id = :id            
            ";
    
    $values = $DB->get_records_sql($sql, ['id' => $report->id]); 
    $values = array_values($values);      
    $report->mainactors = $values[2]->response;
    $report->mainidea = $values[3]->response;
    $report->quotes = $values[4]->response;
    $report->conclusion = $values[5]->response . '/' . $values[6]->response;
}
//print_r($questionnairereports); die;

$reports = array_merge($reports, $questionnairereports);
//print_r(($reports)); die;