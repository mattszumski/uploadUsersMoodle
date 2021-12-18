<?php

namespace local_uploadusers;

use stdClass;

defined ('MOODLE_INTERNAL') || die();

     class Insert_upload {

        private $arrayToInsert = null;

        public function __construct($validatedRecords) {
            $this->arrayToInsert = $validatedRecords;
        }

        public function insertUploadIntoDB(){
            global $DB;
            $positionsAndOrgsFromDB = $this->prepareAndInsertPositionsAndOrgUnits();
            $dataObject = $this->prepareDataObjectForInsert($positionsAndOrgsFromDB);
            $insertResult = $this->insertDataIntoDB($dataObject);
            return $insertResult;

        }

        private function prepareAndInsertPositionsAndOrgUnits() {
            $posAndOrgsForInsert =  $this->preparePositionsAndOrgUnits();
            $result = $this->insertPosAndOrgs($posAndOrgsForInsert);
            return $result;
            

        }
        private function preparePositionsAndOrgUnits(){
            //TODO Check if the given position or organizational_unit is already in the database -> if so, don't add it,
            //but as program uses IDs it later it needs to be stored and passed

            $positionsAndOrgs = new stdClass();
            $positionsAndOrgs->orgs = array();
            $positionsAndOrgs->positions = array();

            $positions = array();
            $orgs = array();

            foreach($this->arrayToInsert as $row) {
                if(!empty($row[5])) {
                    array_push($orgs, $row[5]);
                }
                if(!empty($row[6])) {
                    array_push($positions, $row[6]);
                }
            }


            $orgs = array_unique($orgs);
            $positions = array_unique($positions);

            //inserting arrays of orgs and positions into data object so I could use him to get records from the DB easier
            
            $positionsAndOrgs->dataForGetRecords->orgs= $orgs;
            $positionsAndOrgs->dataForGetRecords->positions = $positions;

            foreach($orgs as $org) {
                $obj = new stdClass();
                $obj->organizational_unit = $org;
                array_push($positionsAndOrgs->orgs, $obj);
            }
            

            foreach($positions as $position) {
                $obj = new stdClass();
                $obj->position = $position;
                array_push($positionsAndOrgs->positions, $obj);
            }


            return $positionsAndOrgs;
        }

        private function insertPosAndOrgs($preparedDataObject){
            global $DB;
            $posAndOrgsDBObject = new stdClass();
            $DB->insert_records('organizational_unit', $preparedDataObject->orgs);
            $DB->insert_records('position', $preparedDataObject->positions);
     
            $posAndOrgsDBObject->orgs = $DB->get_records_list('organizational_unit','organizational_unit', $preparedDataObject->dataForGetRecords->orgs);
            $posAndOrgsDBObject->positions = $DB->get_records_list('position','position', $preparedDataObject->dataForGetRecords->positions);
            
            return $posAndOrgsDBObject;
        }

        private function prepareDataObjectForInsert($posAndOrgsDBIds) {
            $objectsArray = array();
            $DBColumns = array('username', 'email', 'firstname','lastname','employee_number','position_id','organizational_unit_id');
            $dataObjectForInsert = array();
            $organizationalUnitIds = $this->getOrgIds($posAndOrgsDBIds->orgs);
            $positionIds = $this->getPositionIds($posAndOrgsDBIds->positions);

            foreach($this->arrayToInsert as $row){
                $tableInsertRow = new stdClass();
                $tableInsertRow->username = $row[0];
                $tableInsertRow->email = $row[1];
                $tableInsertRow->firstname = $row[2];
                $tableInsertRow->lastname = $row[3];
                $tableInsertRow->employee_number = null;
                $tableInsertRow->organizational_unit_id = null;
                $tableInsertRow->position_id = null;

                if(!empty($row[4])) {
                    $tableInsertRow->employee_number = $row[4];
                }
                if(!empty($row[5])) {
                    $tableInsertRow->organizational_unit_id = $organizationalUnitIds[$row[5]];
                }
                if(!empty($row[6])) {
                    $tableInsertRow->position_id = $positionIds[$row[6]];
                }
                array_push($dataObjectForInsert, $tableInsertRow);
            }
            return $dataObjectForInsert;
        }

        private function getOrgIds($orgsFromDB) {
            $orgIds = array();
            foreach($orgsFromDB as $orgObject) {
                $isDuplicated = array_key_exists($orgObject->organizational_unit, $orgIds);
                if(!$isDuplicated) {
                    $orgIds[$orgObject->organizational_unit] = $orgObject->id;
                }
            }
            return $orgIds;
        }

        private function getPositionIds($positionsFromDB) {
            $positionIds = array();
            foreach($positionsFromDB as $positionObject) {
                $isDuplicated = array_key_exists($positionObject->position, $positionIds);
                if(!$isDuplicated) {
                    $positionIds[$positionObject->position] = $positionObject->id;
                }
            }
            return $positionIds;
        }

        private function insertDataIntoDB($dataObject){
            global $DB;
            $load = $DB->insert_records('user', $dataObject);
            return $load;
        }
     }