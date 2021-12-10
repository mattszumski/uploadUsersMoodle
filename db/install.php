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
 * 
 *
 * @package    local_uploadusers
 * @author     MSzumski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined ('MOODLE_INTERNAL') || die();

 function xmldb_local_uploadusers_install() {
     global $DB;

    $dbman = $DB->get_manager();
    $positionTableId = new xmldb_field('id',XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
    $positionTablePosition = new xmldb_field('position',XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL);
    $positionTablePrimaryKey = new xmldb_key('primary', XMLDB_KEY_PRIMARY, array('id'), null, null);

    $positionTable = new xmldb_table('position');
    $positionTable->addField($positionTableId);
    $positionTable->addField($positionTablePosition);
    $positionTable->addKey($positionTablePrimaryKey);

    $positionTableStatus = $dbman->create_table($positionTable);

    $organizationalUnitTableId = new xmldb_field('id',XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
    $organizationalUnitTableOrganizationalUnit = new xmldb_field('organizational_unit',XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL);
    $organizationalUnitTablePrimaryKey = new xmldb_key('primary', XMLDB_KEY_PRIMARY, array('id'), null, null);

    $organizationalUnitTable = new xmldb_table('organizational_unit');
    $organizationalUnitTable->addField($organizationalUnitTableId);
    $organizationalUnitTable->addField($organizationalUnitTableOrganizationalUnit);
    $organizationalUnitTable->addKey($organizationalUnitTablePrimaryKey);

    $organizationalUnitTableStatus = $dbman->create_table($organizationalUnitTable);


    $userTable = new xmldb_table('user');

    $positionIdField = new xmldb_field('position_id', XMLDB_TYPE_INTEGER, 10);
    $dbman->add_field($userTable, $positionIdField);
    $positionIdKey = new xmldb_key('position_id',XMLDB_KEY_FOREIGN,array('position_id'),'position', array('position'));
    $dbman->add_key($userTable, $positionIdKey);

    $organizationalUnitIdField = new xmldb_field('organizational_unit_id', XMLDB_TYPE_INTEGER, 10);
    $dbman->add_field($userTable, $organizationalUnitIdField);
    $organizationalUnitIdKey = new xmldb_key('organizational_unit_id',XMLDB_KEY_FOREIGN,array('organizational_unit_id'),'organizational_unit', array('organizational_unit'));
    $dbman->add_key($userTable, $organizationalUnitIdKey);

    $employeeNumberField = new xmldb_field('employee_number',XMLDB_TYPE_CHAR,255,null,XMLDB_NOTNULL);
    $dbman->add_field($userTable, $employeeNumberField);


 }