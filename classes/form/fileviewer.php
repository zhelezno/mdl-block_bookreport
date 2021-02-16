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


require_once("$CFG->libdir/formslib.php");
 
class fileviewer extends moodleform {
    
    public function definition() {
        global $CFG;
 
        $mform = $this->_form;

        $filemanageropts['maxfiles'] = 1;
        $filemanageropts['accepted_types'] = '.pptx';

        $mform->addElement('header', 'presheader', get_string('presreport', 'block_bookreport'));
        
        $mform->addElement('text', 'author', get_string('author', 'block_bookreport'));
        $mform->addRule('author', null, 'required', null, 'client');

        $mform->addElement('text', 'book', get_string('book', 'block_bookreport'));
        $mform->addRule('book', null, 'required', null, 'client');

        $mform->addElement('filemanager', 'attachment', get_string('presentation', 'block_bookreport'), null, $filemanageropts);
        $mform->addRule('attachment', null, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('updateform', 'block_bookreport'));        
    }
}