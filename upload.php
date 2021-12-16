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

    require_once(__DIR__ . '/../../config.php');
    require_once(__DIR__ . '/upload_form.php');
    require_once(__DIR__ . '/lib.php');

    $page_title = get_string("uploadusers:pagetitle", 'local_uploadusers');
    $context = context_system::instance();
  
    require_login();

    //TODO
    //Looks fine but throws an error -> check why
    //     require_capability('local/uploaduser:canupload', context_system::instance());
    


    $PAGE->set_url(new moodle_url('/local/uploadusers/upload.php'));
    $PAGE->set_context($context);
    $PAGE->set_title($page_title);

    $mform = new upload_form();


    echo $OUTPUT->header();


    if($formdata = $mform->get_data()) {
        $contextId = $context->id;
    $formItemName = 'attachementUpload';
    $filearea = 'draft';
    $componentName = 'local_uploadusers';
    $filepath = '/';
    $filename = null; // get it from uploaded file
    $itemIdNew = $formdata->attachementUpload;
    
    $mform->save_stored_file($formItemName, $contextId, $componentName,$filearea,$itemIdNew,$filepath,$filename);
    
    $files = get_file_storage()->get_area_files($contextId, $componentName, $filearea, $itemIdNew);

    

    $file = end($files);

    $csvArray = array();

    $openFile = $file->get_content_file_handle();

    if ($openFile != null) {
        while($line = fgets($openFile)) {
            $csvArray[] = str_getcsv($line);
        }
        
    } else {
        echo 'nope.';
    }
    
    fclose($openFile);

    echo 'FILE READ: <br>';
    var_dump($csvArray);

    //TODO call for validation
    //TODO call for insert into db
        
    } else {

        $mform->set_data($data);
        $mform->display();
    }


    echo $OUTPUT->footer();

    