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
 * Block for displaying earned local badges to users
 *
 * @package    block_bookreport
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Chasnikov Andrey
 */

class block_bookreport extends block_base {

    function init() {
        $this->title = '<a href="../blocks/bookreport/index.php">' . get_string('pluginname', 'block_bookreport') . "</a>";
    }

    function has_config() {
        return true;
    }

    function get_content() {
        global $CFG, $DB, $USER;

        require_once($CFG->libdir . '/filelib.php');

        if ($this->content !== NULL) {
            return $this->content;
        }        

        $this->content = new stdClass;
        $this->content->text = '';


        $info = '';
        $info .= '<h5>'. get_string('lastreports', 'block_bookreport') .'</h5>';

        $params = [
            'userid' => $USER->id
        ];
        
        $sql =" SELECT bb.id, bb.type, bb.timecreated, bb.type, bs.author, bs.book
                FROM {block_bookreport} bb
                JOIN {block_bookreport_strep} bs ON (bs.bookreportid = bb.id) 
                WHERE bb.user_id = :userid
                LIMIT 5               
        ";

        $lastreports = $DB->get_records_sql($sql, $params);        
        //$lastreports = json_decode($lastreports, true);
        //print_r($lastreports); die();    
        $reports = '';

        foreach ($lastreports as $report) {
            $reports .= $report->author . ' ' . $report->book . '. Стандартный отчет. ' .date('jS F Y h:i:s A (T)', $report->timecreated) . '<br>';                   
        }
        
        $info .= $reports;

        $this->content->text .= $info;
        $this->content->footer = '';               

        return $this->content;
    }
}