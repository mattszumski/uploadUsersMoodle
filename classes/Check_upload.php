<?php

/**
 * Capability definitions
 *
 * @package    local_uploadusers
 * @author     MSzumski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace local_uploadusers;

 class Check_upload {
     
    private $arrayToCheck = null;
    private $checks = null;
    private $badRows = null;

    public function __construct($csvArray) {
        $this->arrayToCheck = $csvArray;
        $this->checks = new \stdClass();
        $this->checks->errors = array();
        $this->checks->warnings = array();
        $this->checks->successfullyChecked = array();
    }

    public function perform_checks() {
        $arrayLength = count($this->arrayToCheck);
        $usernames = array();

        for($i = 0; $i < $arrayLength; $i++) {
            if($this->checkForTooShortRows($i)) {
                continue;
            }
            if($this->checkIfEssetnialColumnsAreFilled($i)) {
                continue;
            }
            if($this->checkIfUsernameIsDoubled($usernames, $i)) {
                continue;
            }
            $this->checkIfOptionalColumnsAreFilled($i);
        }
        $this->checks->successfullyChecked = array_values($this->arrayToCheck);


        return $this->checks;
    }


    private function checkForTooShortRows($i) {
        if( count($this->arrayToCheck[$i]) < 4) {
            $this->moveRowToErrors($i);
            return 1;
        }
        return 0;    
    }

    private function checkIfEssetnialColumnsAreFilled($i) {   
        if(empty($this->arrayToCheck[$i][0]) || empty($this->arrayToCheck[$i][1]) 
        || empty($this->arrayToCheck[$i][2]) || empty($this->arrayToCheck[$i][3])) {
            $this->moveRowToErrors($i);
            return 1;
        }
        return 0;
    }

    private function checkIfUsernameIsDoubled(&$usernames, $i) {
        $usernameFromFile = $this->arrayToCheck[$i][0];
        if(array_search($usernameFromFile, $usernames, true) !== false) {
            $this->moveRowToErrors($i);
            return 1;
        }
        array_push($usernames, $usernameFromFile);
        return 0;
    }

    private function checkIfOptionalColumnsAreFilled($i) {
        if( count(($this->arrayToCheck[$i])) < 7 || empty($this->arrayToCheck[$i][4]) || 
            empty($this->arrayToCheck[$i][5]) || empty($this->arrayToCheck[$i][6])) {
                $this->copyRowToWarnings($i);
        }
    }
    


    
    private function moveRowToErrors($i) {
        $row = $this->arrayToCheck[$i];
        unset($this->arrayToCheck[$i]);
        array_push($this->checks->errors, $row);
    }

    private function copyRowToWarnings($i) {
        $row = $this->arrayToCheck[$i];
        array_push($this->checks->warnings, $row);
    }
}