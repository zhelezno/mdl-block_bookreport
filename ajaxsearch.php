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


define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php'); 

global $DB;

//Get param
$book = required_param('booksearch', PARAM_TEXT);

//If $book exists
if ($book) {

    //Query param
    $params = [
        'booksearch' => $book
    ];    

    $sql = "SELECT
            c.fullname AS bookfullname
            FROM
            {course} AS c
            WHERE
            c.fullname LIKE 'Книга: %'
            AND
            c.fullname LIKE CONCAT('%', :booksearch, '%')
            LIMIT 6
            ";
    
    $result = $DB->get_records_sql($sql, $params);

    //Result output
    foreach ($result as $row) {
        echo '<a href="#" class="list-group-item list-group-item-action border-1" id="searchresult">' . mb_substr($row->bookfullname, 7) . '</a>';
    }
}