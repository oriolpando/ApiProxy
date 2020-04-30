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
 * APIProxy external functions and service definitions.
 *
 *
 * @package    mod_apiproxy
 * @category   external
 * @copyright   2019-2020 Oriol Pando, Daniel Amo
 * @author      Oriol Pando <oriol.pando@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'mod_apiproxy_view_apiproxy' => array(
        'classname'     => 'mod_apiproxy_external',
        'methodname'    => 'view_apiproxy',
        'description'   => 'Simulate the view.php web interface page: trigger events, completion, etc...',
        'type'          => 'write',
        'capabilities'  => 'mod/apiproxy:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_apiproxy_get_apiproxy_by_courses' => array(
        'classname'     => 'mod_apiproxy_external',
        'methodname'    => 'get_apiproxy_by_courses',
        'description'   => 'Returns a list of pages in a provided list of courses, if no list is provided all pages that the user
                            can view will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/apiproxy:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
);

$services = array( 
    'servicename' => array(
        'functions' => array ('mod_apiproxy_view_apiproxy', 'mod_apiproxy_get_apiproxy_by_courses'), //web service function name
        'requiredcapability' => 'mod/apiproxy:view',                  
        'restrictedusers' =>1,
        'enabled'=>0, //used only when installing the services
    )
);
