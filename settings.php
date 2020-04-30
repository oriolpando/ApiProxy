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
 * API Proxy module admin settings and defaults
 *
 * @package     mod_apiproxy
 * @copyright   2019-2020 Oriol Pando, Daniel Amo
 * @author      Oriol Pando <oriol.pando@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $displayoptions = resourcelib_get_displayoptions(array(RESOURCELIB_DISPLAY_OPEN, RESOURCELIB_DISPLAY_POPUP));
    $defaultdisplayoptions = array(RESOURCELIB_DISPLAY_OPEN);

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configmultiselect('apiproxy/displayoptions',
        get_string('displayoptions', 'apiproxy'), get_string('configdisplayoptions', 'apiproxy'),
        $defaultdisplayoptions, $displayoptions));

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('apiproxymodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox('apiproxy/printheading',
        get_string('printheading', 'apiproxy'), get_string('printheadingexplain', 'apiproxy'), 1));
    $settings->add(new admin_setting_configcheckbox('apiproxy/printintro',
        get_string('printintro', 'apiproxy'), get_string('printintroexplain', 'apiproxy'), 0));
    $settings->add(new admin_setting_configcheckbox('apiproxy/printlastmodified',
        get_string('printlastmodified', 'apiproxy'), get_string('printlastmodifiedexplain', 'apiproxy'), 1));
    $settings->add(new admin_setting_configselect('apiproxy/display',
        get_string('displayselect', 'apiproxy'), get_string('displayselectexplain', 'apiproxy'), RESOURCELIB_DISPLAY_OPEN, $displayoptions));
    $settings->add(new admin_setting_configtext('apiproxy/popupwidth',
        get_string('popupwidth', 'apiproxy'), get_string('popupwidthexplain', 'apiproxy'), 620, PARAM_INT, 7));
    $settings->add(new admin_setting_configtext('apiproxy/popupheight',
        get_string('popupheight', 'apiproxy'), get_string('popupheightexplain', 'apiproxy'), 450, PARAM_INT, 7));
}
