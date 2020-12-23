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

global $DB,$USER;

$type = 1;
// $author = $_POST['defaulttype_Author'];
// $book = $_POST['defaulttype_Book'];
// $heroes = $_POST['defaulttype_Mainheroes'];
// $idea = $_POST['defaulttype_Mainidea'];
// $quotes = $_POST['defaulttype_quotes'];
// $conclusion = $_POST['defaulttype_Conclusion'];
$user = $USER->id;
$completed = 1;
$timecreated = time();
$timemodified = time();

$params = [
    'userid' => $user,
    'type' => $type,
    'completed' => $completed,
    'timecreated' => $timecreated,
    'timemodified' => $timemodified
];

$sql = "INSERT INTO 
        mdl_block_bookreport(user_id, type, completed, timecreated, timemodified) 
        VALUES(:userid, :type, :completed, :timecreated, :timemodified)
";
//last_insert_id
$DB->execute($sql, $params);

$indexurl = new moodle_url('/blocks/bookreport/index.php');
redirect($indexurl, 'Отчет создан!');