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
 * @package     mod_apiproxy
 * @copyright   2019-2020 Oriol Pando, Daniel Amo
 * @author      Oriol Pando <oriol.pando@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in Page module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function apiproxy_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function apiproxy_reset_userdata($data) {

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    return array();
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function apiproxy_get_view_actions() {
    return array('view','view all');
}

/**
 * Add apiproxy instance.
 * 
 * @param stdClass $data
 * @param mod_apiproxy_mod_form $mform
 * @return int new apiproxy instance id
 */
function apiproxy_add_instance($data, $mform = null) {
    global $CFG, $DB;

    
    require_once("$CFG->libdir/resourcelib.php");


   
    $cmid = $data->coursemodule;
    $data->name = $data->apiname;

    $localparametersget = $data->localparameter;
    $localparameterspost = $data->localparameterpost;

    $error = false;
    if (strcmp($data->apitype,'extern') == 0) {

        $realurl = $data->realurl;
        if (strcmp($realurl, '-') == 0) {
            $error = true;
        }
        $realparametersget = $data->realparameter;
        $realparameterspost = $data->realparameterpost;
    }else{
        $realurl = '-';
    }

    $data->realurl = $realurl;

    
    $data->timemodified = time();


    $fields = array('course', 'name', 'realurl', 'timemodified', 'revision');


    if ($error) {
        redirect($CFG->wwwroot . '/course/view.php?id=' . $data->course, 'Error when creating an apiproxy!');
    }



    $data->id = $DB->insert_record('apiproxy', $data, $fields);



    $paramfields = array('apiid', 'localparameter', 'realparameter', 'type');


    $info = new \stdClass();
    //Inserts GET parameters on table
    for ($i = 0; $i < count($localparametersget); $i++){
        $error = false;
        $info->apiid = $data->id;
        if (strcmp(preg_replace('/\s+/', '',$localparametersget[$i]), '') == 0) {
            $error = true;
        }else{
            $info->localparameter = preg_replace('/\s+/', '',$localparametersget[$i]);
        }
        if (strcmp($realurl, "-") == 0){
            $info->realparameter = '-';
        }else{
            if (strcmp(preg_replace('/\s+/', '',$realparametersget[$i]), '') == 0) {
                $error = true;
            }else{
                $info->realparameter = preg_replace('/\s+/', '',$realparametersget[$i]);
            }
        }
        $info->type = 'GET';
        if (!$error){
            if ($DB->get_record('apiproxy_parameters', array('apiid'=>$info->apiid,'localparameter'=>$info->localparameter, 'type'=>$info->type))) {
                $DB->delete_records('apiproxy_parameters', array('apiid'=>$info->apiid, 'localparameter'=>$info->localparameter, 'type'=>$info->type));
            }
            $DB->insert_record('apiproxy_parameters', $info, $paramfields);
        }
    }


    $info = new \stdClass();
    //Inserts POST parameters on table
    for ($i = 0; $i < count($localparameterspost); $i++){
        $error = false;
        $info->apiid = $data->id;
        if (strcmp(preg_replace('/\s+/', '',$localparameterspost[$i]), '') == 0) {
            $error = true;
        }else{
            $info->localparameter = preg_replace('/\s+/', '',$localparameterspost[$i]);
        }
        if (strcmp($realurl, "-") == 0){
            $info->realparameter = '-';
        }else{
            if (strcmp(preg_replace('/\s+/', '',$realparameterspost[$i]), '') == 0) {
                $error = true;
            }else{
                $info->realparameter = preg_replace('/\s+/', '',$realparameterspost[$i]);
            }
        }
        $info->type = 'POST';
        if (!$error){
            if ($DB->get_record('apiproxy_parameters', array('apiid'=>$info->apiid,'localparameter'=>$info->localparameter, 'type'=>$info->type))) {
                $DB->delete_records('apiproxy_parameters', array('apiid'=>$info->apiid, 'localparameter'=>$info->localparameter, 'type'=>$info->type));
            }
            $DB->insert_record('apiproxy_parameters', $info, $paramfields);
        }
    }
    
    return $data->id;
}

/**
 * Delete apiproxy instance.
 * @param int $id
 * @return bool true
 */
function apiproxy_delete_instance($id) {
    global $DB;
    if (!$apiproxy = $DB->get_record('apiproxy', array('id'=>50))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('apiproxy', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'apiproxy', $id, null);


    $DB->delete_records('apiproxy', array('id'=>$apiproxy->id));
    return true;
}

/**
 * Update apiproxy instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function apiproxy_update_instance($data) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    

    $now = new DateTime("now", core_date::get_server_timezone_object());
    $record = new stdclass;
    if (strcmp(gettype($data), 'array') == 0) {
        $record->id = $data['id'];
        $record->name = $data['name'];
        $record->realurl = $data['realurl'];
        $record->intro = $data['intro'];
        $record->timemodified = $now->getTimestamp();
        $localparametersget = $data['localparameter'];
        $localparameterspost = $data['localparameterpost'];
        if (strcmp($record->realurl, '-') != 0) {
            $realparametersget = $data['realparameter'];
            $realparameterspost = $data['realparameterpost'];
        }

    }else{
        $record->id = $_SESSION['apiproxy']->id;
        $record->name = $data->apiname;
        $record->realurl = $data->realurl;
        $record->intro = $data->intro;
        $record->timemodified = $now->getTimestamp();
        $localparametersget = $data->localparameter;
        $localparameterspost = $data->localparameterpost;
        if (strcmp($record->realurl, '-') != 0) {
            $realparametersget = $data->realparameter;
            $realparameterspost = $data->realparameterpost;
        }
    }

    $realurl = $record->realurl;


    $paramfields = array('apiid', 'localparameter', 'realparameter', 'type');


    $info = new \stdClass();


    //Delete previous parameters
    //$apiproxy = $DB->delete_records('apiproxy_parameters', array('apiid'=>$record->id));

    //Inserts GET parameters on table
    for ($i = 0; $i < count($localparametersget); $i++){
        $error = false;
        $info->apiid = $record->id;
        if (strcmp(preg_replace('/\s+/', '',$localparametersget[$i]), '') == 0) {
            $error = true;
        }else{
            $info->localparameter = preg_replace('/\s+/', '',$localparametersget[$i]);
        }
        if (strcmp($realurl, "-") == 0){
            $info->realparameter = '-';
        }else{
            if (strcmp(preg_replace('/\s+/', '',$realparametersget[$i]), '') == 0) {
                $error = true;
            }else{
                $info->realparameter = preg_replace('/\s+/', '',$realparametersget[$i]);
            }
        }
        $info->type = 'GET';
        if (!$error){
            if ($DB->get_record('apiproxy_parameters', array('apiid'=>$info->apiid,'localparameter'=>$info->localparameter, 'type'=>$info->type))) {
                $DB->delete_records('apiproxy_parameters', array('apiid'=>$info->apiid, 'localparameter'=>$info->localparameter, 'type'=>$info->type));
            }
            $DB->insert_record('apiproxy_parameters', $info, $paramfields);
        }
    }

    $info = new \stdClass();
    //Inserts POST parameters on table
    for ($i = 0; $i < count($localparameterspost); $i++){
        $error = false;
        $info->apiid = $record->id;
        if (strcmp(preg_replace('/\s+/', '',$localparameterspost[$i]), '') == 0) {
            $error = true;
        }else{
            $info->localparameter = preg_replace('/\s+/', '',$localparameterspost[$i]);
        }
        if (strcmp($realurl, "-") == 0){
            $info->realparameter = '-';
        }else{
            if (strcmp(preg_replace('/\s+/', '',$realparameterspost[$i]), '') == 0) {
                $error = true;
            }else{
                $info->realparameter = preg_replace('/\s+/', '',$realparameterspost[$i]);
            }
        }
        $info->type = 'POST';
        if (!$error){
            if ($DB->get_record('apiproxy_parameters', array('apiid'=>$info->apiid, 'localparameter'=>$info->localparameter, 'type'=>$info->type))) {
                $DB->delete_records('apiproxy_parameters', array('apiid'=>$info->apiid, 'localparameter'=>$info->localparameter, 'type'=>$info->type));
            }
            $DB->insert_record('apiproxy_parameters', $info, $paramfields);
        }
    }


    $DB->update_record('apiproxy', $record);

    return true;
}



function apiproxy_view($apiproxy, $course, $cm, $context) {
    global $CFG, $DB;


    //GET
    $select = "apiid = ? AND type = 'GET'";
    $params = array($apiproxy->id);
    $info = $DB->get_records_select('apiproxy_parameters', $select, $params, $sort='', $fields='*', $limitfrom=0, $limitnum=0);
    $info = array_values($info);

    for ($i = 0; $i < count($info); $i++){
        $apiproxy->getparameterslocal[$i] = $info[$i]->localparameter;
        $apiproxy->getparametersreal[$i] = $info[$i]->realparameter;
    }


    $select = "apiid = ? AND type = 'POST'";
    $params = array($apiproxy->id);
    $info = $DB->get_records_select('apiproxy_parameters', $select, $params, $sort='', $fields='*', $limitfrom=0, $limitnum=0);
    $info = array_values($info);

    for ($i = 0; $i < count($info); $i++){
        $apiproxy->postparameterslocal[$i] = $info[$i]->localparameter;
        $apiproxy->postparametersreal[$i] = $info[$i]->realparameter;
    }

    if (strcmp($apiproxy->realurl, '-') == 0) {
        $apiproxy->type = "Intern (1st Person API)";
    }else{
        $apiproxy->type = "Extern (3rd Person API)";
    }

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $apiproxy->id
    );



    $event = \mod_apiproxy\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('apiproxy', $apiproxy);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Get apiproxy instance.
 * @param int $id
 * @return bool true
 */
function apiproxy_get_instance($id) {
    global $DB;

    $params = array('id'=>$id);
    $info = $DB->get_record('apiproxy',$params);

    return $info;
}

/**
 * Get apiproxy type.
 * @param int $id
 * @return bool 
 */
function apiproxy_get_type($id) {
    global $DB;

    $params = array('id'=>$id);
    $info = $DB->get_record('apiproxy',$params);

    if (strcmp($info->realurl, '-') == 0) {
        return true;
    }
    return $info->realurl;
}

/**
 * Get apiproxy info.
 * @param int $id
 */
function apiproxy_get_info($id) {
    global $CFG, $DB;


    //GET

    $select = "apiid = ? AND type = 'GET'";
    $params = array($id);
    $info = $DB->get_records_select('apiproxy_parameters', $select, $params, $sort='', $fields='*', $limitfrom=0, $limitnum=0);
    $info = array_values($info);

    for ($i = 0; $i < count($info); $i++){
        $apiproxy->getparameterslocal[$i] = $info[$i]->localparameter;
        $apiproxy->getparametersreal[$i] = $info[$i]->realparameter;
    }


    $select = "apiid = ? AND type = 'POST'";
    $params = array($id);
    $info = $DB->get_records_select('apiproxy_parameters', $select, $params, $sort='', $fields='*', $limitfrom=0, $limitnum=0);
    $info = array_values($info);

    for ($i = 0; $i < count($info); $i++){
        $apiproxy->postparameterslocal[$i] = $info[$i]->localparameter;
        $apiproxy->postparametersreal[$i] = $info[$i]->realparameter;
    }

    return $apiproxy;
}



/**
 * Add apiproxy log.
 * 
 * @param stdClass $data
 * @param mod_apiproxy_mod_form $mform
 * @return int new apiproxy instance id
 */
function apiproxy_add_log($info) {
    global $CFG, $DB;

    $fields = array('apiid','userid','type','comment','logtime');

    $DB->insert_record('apiproxy_logs', $info, $fields);
    
    return true;
}

/**
 * get apiproxy logs.
 * 
 * @param stdClass $data
 * @param mod_apiproxy_mod_form $mform
 * @return int new apiproxy instance id
 */
function apiproxy_get_log($id, $lim) { 
    global $CFG, $DB;

    $select = "apiid = ?";
    $params = array($id);
    $info = $DB->get_records_select('apiproxy_logs', $select, $params, $sort='', $fields='*', $limitfrom=0, $limitnum=$lim);

    return $info;
}

/**
 * get user name.
 * 
 * @param stdClass $data
 * @param mod_apiproxy_mod_form $mform
 * @return int new apiproxy instance id
 */
function apiproxy_get_username($id) { 
    global $CFG, $DB;

    $info = $DB->get_record('user', array('id'=>$id));

    return $info;
}
