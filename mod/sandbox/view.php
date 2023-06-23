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
 * Prints a particular instance of sandbox.
 *
 * @package    mod_sandbox
 * @copyright  2019 Richard Jones richardnz@outlook.com
 * @copyright  2021 G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_simplemod
 * @see https://github.com/justinhunt/moodle-mod_simplemod
 * @see https://github.com/richardjonesnz/moodle-mod_simplemod
 * @see https://github.com/gjb2048/moodle-mod_simplemod
 */

use mod_sandbox\output\view;
require_once('../../config.php');

// We need the Sandbox instance id.
$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('sandbox', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$sandbox = $DB->get_record('sandbox', ['id' => $cm->instance], '*', MUST_EXIST);

// Print the page header.
$PAGE->set_url('/mod/sandbox/view.php', array('id' => $cm->id));

require_login($course, true, $cm);

// Set the page information.
$PAGE->set_title(format_string($sandbox->name));
$PAGE->set_heading(format_string($course->fullname));

// Check for intro page content.
if (!$sandbox->intro) {
    $sandbox->intro = '';
}
// Start output to browser.
echo $OUTPUT->header();

// Call classes/output/view and view.mustache to create output.
echo $OUTPUT->render(new view($sandbox, $cm->id));

// End output to browser.
echo $OUTPUT->footer();
