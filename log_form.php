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
 * API Proxy configuration form
 *
 * @package     mod_apiproxy
 * @copyright   2019 Oriol Pando, Daniel Amo
 * @author      Oriol Pando <oriol.pando@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/apiproxy/locallib.php');
require_once($CFG->dirroot.'/mod/apiproxy/lib.php');



class mod_apiproxy_log_form extends moodleform{
    //Add elements to form
    public function definition() {
        global $CFG;

        $config = get_config('apiproxy');

        $cm = context_module::instance($_SESSION['cmid']);

        $users = get_enrolled_users($cm);

        $mform = $this->_form; // Don't forget the underscore! 

        //$mform->addElement('submit', 'search', 'Search');

        /*
        $selection = array();
        array_push($selection, "All");
        foreach ($users as $element) {
            array_push($selection, $element->firstname . ' ' . $element->lastname);
        }
        $select = $mform->addElement('select', 'colors', 'Users', $selection, $attributes);
        $select->setMultiple(true);
        */ 

        $info = apiproxy_get_log($_SESSION['apid'], 0);
        $logs = array();
        $htmlString = '<table><tr><th>User</th><th>Call Type</th><th>Content</th><th>Log Time</th></tr>';
        foreach ($info as $element) { 

            $user = apiproxy_get_username($element->userid);
            
            if(!$user){
                $username = '-'; 
            }else{
                $username = $user->firstname . ' ' . $user->lastname;
            }

            $time = date("F j, Y, g:i a", $element->logtime);
            $htmlString .= '<tr><td>' . $username . '</td><td>' . $element->type . '</td>
                <td>' . $element->comment . '</td><td>' . $time . '</td></tr>';

            
            $log = new stdClass();
            $log->user = $username;
            $log->type = $element->type;
            $log->comment = $element->comment;
            $log->time = $time;

            $logs[] = $log;
        }
        
        $mform->addElement('html', $htmlString . '</table>');

        $mform->addElement('html', '<a class="qheader">');

        $_SESSION['student_logs'] = $logs;
        //$mform->addElement('submit', 'download', get_string('downloadLogs','apiproxy'));

    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
