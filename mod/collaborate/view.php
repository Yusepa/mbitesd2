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
 * Prints a particular instance of collaborate.
 *
 * @package    mod_collaborate
 * @copyright  2019 Richard Jones richardnz@outlook.com
 * @copyright  2021 G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate
 * @see https://github.com/richardjonesnz/moodle-mod_collaborate
 * @see https://github.com/gjb2048/moodle-mod_collaborate
 */

use mod_collaborate\output\view;
require_once('../../config.php');

// We need the course module id (id).
$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('collaborate', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$collaborate = $DB->get_record('collaborate', ['id' => $cm->instance], '*', MUST_EXIST);

if ($id) {
    $cm = get_coursemodule_from_id('collaborate', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $collaborate = $DB->get_record('collaborate', array('id' => $cm->instance), '*', MUST_EXIST);
}

// Print the page header.
$PAGE->set_url('/mod/collaborate/view.php', array('id' => $cm->id));

require_login($course, true, $cm);
require_login($course, true, $cm);

// Set the page information.
$PAGE->set_title(format_string($collaborate->name));
$PAGE->set_heading(format_string($course->fullname));

// Let's consider the activity "viewed" at this point.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Let's add the module viewed event.
$event = \mod_collaborate\event\page_viewed::create(['context' => $PAGE->context, 'objectid' => $collaborate->id]);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $collaborate);
$event->trigger();

// Check for intro page content.
if (!$collaborate->intro) {
    $collaborate->intro = '';
}

// Show reports tab if permission exists and admin has allowed.
$reportstab = false;
$config = get_config('mod_collaborate');
if ($config->enablereports) {
    if (has_capability('mod/collaborate:viewreportstab', $PAGE->context)) {
        $reportstab = true;
    }
}

// Start output to browser.
echo $OUTPUT->header();

// Call classes/output/view and view.mustache to create output.
echo $OUTPUT->render(new view($collaborate, $cm->id, $reportstab));

// End output to browser.
echo $OUTPUT->footer();
