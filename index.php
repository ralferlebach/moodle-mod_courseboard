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
 * This page lists all the instances of courseboard in a particular course
 *
 * @author  Franz Weidmann
 * @package mod_courseboard
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // Course.

global $DB;

if (! $course = $DB->get_record('course', 'id', $id)) {
    error('Course ID is incorrect');
}

require_course_login($course);

add_to_log($course->id, 'courseboard', 'view all', 'index.php?id=$course->id', '');


// Get all required strings.

$strcoursemodules = get_string('modulenameplural', 'courseboard');
$strcoursemodule  = get_string('modulename', 'courseboard');


// Print the header.

$navlinks = array();
$navlinks[] = array('name' => $strcoursemodules, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);

print_header_simple($strcoursemodules, '', $navigation, '', '', true, '', navmenu($course));

// Get all the appropriate data.

if (! $coursemodules = get_all_instances_in_course('courseboard', $course)) {
    notice('There are no instances of coursemodule', '../../course/view.php?id=$course->id');
    die;
}

// Print the list of instances (your module will probably extend this).

$timenow  = time();
$strname  = get_string('name');
$strweek  = get_string('week');
$strtopic = get_string('topic');

if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname);
    $table->align = array ('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array ($strtopic, $strname);
    $table->align = array ('center', 'left', 'left', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');
}

foreach ($coursemodules as $courseboard) {
    if (!$courseboard->visible) {
        // Show dimmed if the mod is hidden.
        $link = "<a class = 'dimmed' href = 'view.php ? id = ".$courseboard->coursemodule."'>".format_string($courseboard->name)."</a>";
    } else {
        // Show normal if the mod is visible.
        $link = "<a href ='view.php ? id = ".$courseboard->coursemodule."'>".format_string($courseboard->name)."</a>";
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array ($courseboard->section, $link);
    } else {
        $table->data[] = array ($link);
    }
}

print_heading($strcourseboards);
print_table($table);

// Finish the page.

print_footer($course);
