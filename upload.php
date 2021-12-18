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


    use local_uploadusers\Check_upload;
    use local_uploadusers\Insert_upload;

    require_once(__DIR__ . '/../../config.php');
    require_once(__DIR__ . '/upload_form.php');
    require_once(__DIR__ . '/lib.php');


    $page_title = get_string("uploadusers:pagetitle", 'local_uploadusers');
    $context = context_system::instance();
  
    require_login();
    require_capability('local/uploadusers:canupload', context_system::instance());
    


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

        if($file->get_filesize() > 5242880) {
            $fileTooBigData = (object)[
                'error' => "File is larger than 5mb. Please upload smaller file.",
                'url' =>new moodle_url('/local/uploadusers/upload.php')
            ];
    
            echo $OUTPUT->render_from_template('local_uploadusers/filetoobig', $fileTooBigData);
        } else {

        $csvArray = array();

        $openFile = $file->get_content_file_handle();

        if ($openFile != null) {
            while($line = fgets($openFile)) {
                $csvArray[] = str_getcsv($line);
            }
        } else {
            echo 'Error during opening uploaded file.';
        }
        
        fclose($openFile);

        //TODO call for validation

        $checks = new Check_upload($csvArray);
        $result = $checks->perform_checks();

        //TODO call for insert into db

        $insertIntoDB = new Insert_upload($result->successfullyChecked);
        $DBResult = $insertIntoDB->insertUploadIntoDB();

        $successData = (object)[
            'errors' => $result->errors,
            'warnings' => $result->warnings,
            'url' =>new moodle_url('/local/uploadusers/upload.php')
        ];
    
        echo $OUTPUT->render_from_template('local_uploadusers/success', $successData);
        }

    } else {

        $mform->set_data($data);
        $mform->display();
    }


    echo $OUTPUT->footer();

    