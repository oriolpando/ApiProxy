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



class mod_apiproxy_view_form extends moodleform{
    //Add elements to form
    public function definition() {
        global $CFG;

        $config = get_config('apiproxy');

 
        $mform = $this->_form; // Don't forget the underscore! 

        $apiproxy = $_SESSION['apiproxy'];
        
        $edit = false;
        $mform->addElement('hidden', 'edit');
        $mform->setType('edit', PARAM_TEXT);
        $mform->setDefault('edit', "false");


        $context = context_course::instance($apiproxy->course);
        if (has_capability('moodle/course:update', $context)) {
            $edit = true;
            $mform->setDefault('edit', "true");

        }
        //-------------------------------------------------------
        if ($edit) {
            $mform->addElement('text', 'apiname', get_string('apiname', 'apiproxy'), array('size'=>'48'));
            $mform->setType('apiname', PARAM_TEXT);
            $mform->setDefault('apiname', $apiproxy->name);
            $mform->addRule('apiname', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
            $mform->disabledIf('apiname', 'edit', 'eq', 'false');

        }

        $mform->addElement('static', 'APIurl', get_string('apiurl', 'apiproxy'), array('size'=>'48'));
        $mform->setDefault('APIurl', $CFG->wwwroot . '/mod/apiproxy/call.php?id=' . $apiproxy->id);
        
        if ($edit) {

            $mform->addElement('textarea', 'intro', get_string('introtext', 'apiproxy'), 'wrap="virtual" rows="10" cols="50"');
            $mform->setDefault('intro', $apiproxy->intro);
            $mform->disabledIf('intro', 'edit', 'eq', 'false');

            $mform->addElement('hidden', 'apiurlf');
            $mform->setType('apiurlf', PARAM_TEXT);
            $mform->setDefault('apiurlf', $CFG->wwwroot . '/course/view.php?id=' . $apiproxy->course);
            $mform->addElement('hidden', 'apiid');
            $mform->setType('apiid', PARAM_INT);
            $mform->setDefault('apiid', $apiproxy->id);         


            //Check api type and data
            $realurl = apiproxy_get_type($apiproxy->id);

            if (strcmp($realurl, '-') == 0) {
                $apitype = true;
            }else{
                $apitype = false;
            }


            //-----------------API PROXY-----------------------------
            //-----------------API TYPE---------------
            /*
            $options = array(
                'intern' => 'Intern API (1st Party API)',
                'extern' => 'Extern API (3rd Party API)'
            );
            $select = $mform->addElement('select', 'apitype', get_string('contentheader', 'apiproxy'), $options);
            if ($apitype) {
                $select->setSelected('intern');
            }else{
                $select->setSelected('extern');
            }
            $mform->disabledIf('apitype', 'edit', 'eq', 'false');
            */
            $mform->addElement('hidden', 'apitype');
            $mform->setType('apitype', PARAM_TEXT);
            $mform->setDefault('apitype', 'extern');
            


            $mform->addElement('text', 'realurl', get_string('realapiurl', 'apiproxy'), array('size'=>'48'));
            $mform->setType('realurl', PARAM_TEXT);
            $mform->setDefault('realurl', $apiproxy->realurl);
            $mform->hideif('realurl', 'apitype', 'eq', 'intern');
            //Canviar maxbytes
            $maxbytes = 10485760;
            $mform->addElement('filemanager', 'attachments', get_string('apifiles', 'apiproxy'), null,
                        array('subdirs' => 0, 'maxbytes' => $maxbytes, 'areamaxbytes' => 10485760, 'maxfiles' => 50,
                            'accepted_types' => array('web_file, sourcecode,web_image,json'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL));
            $mform->hideif('attachments', 'apitype', 'eq', 'extern');

        }

        //-----------------API MAPPING---------------        
        //---------------------GET--------
        $mform->addElement('header', 'contentsection3', get_string('contentparametersget', 'apiproxy'));


        $i = 0;
        foreach ($apiproxy->getparameterslocal as $element) {
            $r = $i +1;
            $mform->addElement('static', 'get' . $i, 'GET Parameter ' . $r, array('size'=>'48'));
            if ($edit) {
                $mform->setDefault('get' . $i, $element . '(local) => ' . $apiproxy->getparametersreal[$i] .'(real)');
            }else{
                $mform->setDefault('get' . $i, $element);
            }
            $i++;
        }

        if ($edit) {
            $attribute = array('onclick'=>'updateGetParams();');
            $mform->addElement('button', 'updateGet', 'Update GET parameters', $attribute);
            $mform->addHelpButton('updateGet', 'updateGet', 'apiproxy');

            $mform->addElement('html', '<div id="updateGet"  style="display:none">');

            $repeatarray = array();

            $repeatarray[] = $mform->createElement('text', 'localparameter', get_string('localparametersget', 'apiproxy'));
            $repeatarray[] = $mform->createElement('text', 'realparameter', get_string('realparametersget', 'apiproxy'));
            $repeatarray[] = $mform->createElement('hidden', 'optionid', 1);

            $repeatno = 1;

            $repeateloptions = array();
            $repeateloptions['localparameter']['default'] = 'parameter{no}';
            $repeateloptions['localparameter']['type'] = PARAM_TEXT;
            $repeateloptions['realparameter']['type'] = PARAM_TEXT;
            $repeateloptions['realparameter']['hideif'] = array('apitype', 'eq', 'intern');

            $mform->setType('option', PARAM_CLEANHTML);

            $mform->setType('optionid', PARAM_INT);

            $this->repeat_elements($repeatarray, $repeatno,
                        $repeateloptions, 'get_repeats', 'get_add_fields', 1, null, true);

            $mform->addElement('html', '</div>');
        }


        $mform->addElement('header', 'contentsection3', get_string('contentparameterspost', 'apiproxy'));

        //---------------------POST--------
        $i = 0;
        foreach ($apiproxy->postparameterslocal as $element) {
            $r = $i +1;
            $mform->addElement('static', 'post' . $i, 'POST Parameter ' . $r, array('size'=>'48'));

            if ($edit) {
                $mform->setDefault('post' . $i, $element . '(local) => ' . $apiproxy->postparametersreal[$i] .'(real)');
            }else{
                $mform->setDefault('post' . $i, $element);
            }
            $i++;
        }
        if ($edit) {
            $attribute = array('onclick'=>'updatePostParams();');
            $mform->addElement('button', 'updatePost', 'Update POST parameters', $attribute);
            $mform->addHelpButton('updatePost', 'updatePost', 'apiproxy');

            $mform->addElement('html', '<div id="updatePost"  style="display:none">');
            $repeatarraypost = array();
            $repeatarraypost[] = $mform->createElement('text', 'localparameterpost', get_string('localparameterspost', 'apiproxy'));
            $repeatarraypost[] = $mform->createElement('text', 'realparameterpost', get_string('realparameterspost', 'apiproxy'));
            $repeatarraypost[] = $mform->createElement('hidden', 'optionidpost', 1);


            $repeatnopost = 1;

            $repeateloptionspost = array();
            $repeateloptionspost['localparameterpost']['default'] = 'parameter{no}';
            $repeateloptionspost['localparameterpost']['type'] = PARAM_TEXT;
            $repeateloptionspost['realparameterpost']['type'] = PARAM_TEXT;
            $repeateloptionspost['realparameterpost']['hideif'] = array('apitype', 'eq', 'intern');

            $mform->setType('optionpost', PARAM_CLEANHTML);

            $mform->setType('optionidpost', PARAM_INT);

            $this->repeat_elements($repeatarraypost, $repeatnopost, $repeateloptionspost, 'post_repeats', 'post_add_fields', 1, null, true);
            $mform->addElement('html', '</div>');

        }   
        
        $repeatarray = array();
    
        /*
        

        $repeatarray[] = $mform->createElement('text', 'localparameter', get_string('localparametersget', 'apiproxy'));
        $repeatarray[] = $mform->createElement('text', 'realparameter', get_string('realparametersget', 'apiproxy'));
        $repeatarray[] = $mform->createElement('hidden', 'optionid', 1);

        $repeatno = 1;

        $repeateloptions = array();
        $repeateloptions['localparameter']['type'] = PARAM_TEXT;
        $repeateloptions['realparameter']['type'] = PARAM_TEXT;
        $repeateloptions['realparameter']['hideif'] = array('apitype', 'eq', 'intern');

        $mform->setType('option', PARAM_CLEANHTML);

        $mform->setType('optionid', PARAM_INT);

        $this->repeat_elements($repeatarray, $repeatno,
                    $repeateloptions, 'get_repeats', 'get_add_fields', 1, null, true);

        //---------------------POST--------

        $repeatarraypost = array();
        $repeatarraypost[] = $mform->createElement('text', 'localparameterpost', get_string('localparameterspost', 'apiproxy'));
        $repeatarraypost[] = $mform->createElement('text', 'realparameterpost', get_string('realparameterspost', 'apiproxy'));
        $repeatarraypost[] = $mform->createElement('hidden', 'optionidpost', 1);


        $repeatnopost = 1;

        $repeateloptionspost = array();
        $repeateloptionspost['localparameterpost']['default'] = 'parameter{no}';
        $repeateloptionspost['localparameterpost']['type'] = PARAM_TEXT;
        $repeateloptionspost['realparameterpost']['type'] = PARAM_TEXT;
        $repeateloptionspost['realparameterpost']['hideif'] = array('apitype', 'eq', 'intern');

        $mform->setType('optionpost', PARAM_CLEANHTML);

        $mform->setType('optionidpost', PARAM_INT);

        $this->repeat_elements($repeatarraypost, $repeatnopost, $repeateloptionspost, 'post_repeats', 'post_add_fields', 1, null, true);

        */


        //-------------------------------------------------------
        $mform->addElement('hidden', 'revision');
        $mform->setType('revision', PARAM_INT);
        $mform->setDefault('revision', 1);
        $mform->addElement('hidden', 'cm');
        $mform->setType('cm', PARAM_INT);
        $mform->setDefault('cm', $apiproxy->cm->id);

        $this->add_action_buttons();

        if ($edit) {
            $mform->addElement('submit', 'gotologs', get_string('gotologs','apiproxy'));
        }

    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}

