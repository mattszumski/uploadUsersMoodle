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
     *  local_uploadusers
     *
     *  This plugin will import user enrollments and group assignments
     *  from a delimited text file. It does not create new user accounts
     *  in Moodle, it will only enroll existing users in a course.
     *
     * @package    local_uploadusers
     * @author     MSzumski
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */

    require_once($CFG->libdir.'/formslib.php');



    
    class upload_form extends moodleform {

        public function definition() {
            global $CFG;

            $mform = $this->_form;

            $options = array(
                'maxbytes' => 5242880,
                'accepted_types' => array('csv','txt')
            );

            $mform->addElement('filepicker',  'attachementUpload',  'upload',  null, $options);
            $this->add_action_buttons(false,'Upload');
        }

        

    }