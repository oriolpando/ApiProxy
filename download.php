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
 * API proxy module version information
 *
 * @package     mod_apiproxy
 * @copyright   2019 Oriol Pando, Daniel Amo
 * @author      Oriol Pando <oriol.pando@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require('../../config.php');
require_once($CFG->dirroot.'/mod/apiproxy/lib.php');
require_once($CFG->dirroot.'/mod/apiproxy/locallib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/dataformatlib.php');

//die();
$dataformat = optional_param('dataformat', '', PARAM_ALPHA);
$columns = array(
    'user' => 'User',
    'type' => 'Type',
    'comment' => 'Comment',
    'time' => 'Time'
);
download_as_dataformat('student_logs', $dataformat, $columns, $_SESSION['student_logs']);

