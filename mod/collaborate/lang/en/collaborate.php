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
 * English strings for collaborate
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_collaborate
 * @copyright  2019 Richard Jones richardnz@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_collaborate
 * @see https://github.com/justinhunt/moodle-mod_collaborate
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'collaborate';
$string['modulenameplural'] = 'collaborates';
$string['modulename_help'] = 'Use the collaborate module for... | The collaborate module allows...';
$string['collaborate:addinstance'] = 'Add a new collaborate';
$string['collaborate:submit'] = 'Submit collaborate';
$string['collaborate:view'] = 'View collaborate';
$string['collaborate:viewreportstab'] = 'View reports tab';
$string['collaboratefieldset'] = 'Custom example fieldset';
$string['collaboratename'] = 'Name';
$string['collaboratename_help'] = 'Updated: This is the content of the help tooltip associated with the collaboratename field.  Markdown syntax is supported.';
$string['collaboratetitle'] = 'Title';
$string['collaboratetitle_help'] = 'This is the plugin title.';
$string['collaborate'] = 'collaborate';
$string['pluginadministration'] = 'collaborate administration';
$string['pluginname'] = 'collaborate';
$string['nocollaborates'] = 'No instances';

// Mod form specific collaborate settings.
$string['title'] = 'Activity title';

// Event
$string['pageviewed'] = 'Activity title';


// editors
$string['title'] = 'Activity Title: ';
$string['texteditor'] = 'Instructions {$a}';

// User button labels.
$string['studenta'] = 'Partner A';
$string['studentb'] = 'Partner B';

// Showpage.
$string['user'] = 'Partner {$a}';
$string['returnview'] = 'Volver';

// Settings
$string['enablereports'] = 'Enable reports';
$string['enablereports_desc'] = 'Enable the reports to be viewed by teachers.';
$string['reportstab'] = 'Reports';
$string['viewtab'] = 'View';
$string['namechange'] = 'Change name';

// Submissions form.
$string['submission'] = 'Your submission';
$string['submissionsave'] = 'Save submission';
$string['submissionupdated'] = 'Submission updated';

// Reports
$string['reporttitle'] = 'Title';
$string['id'] = 'Id';
$string['exportlink'] = 'Export submissions to PDF';

// Grading form.
$string['allocategrade'] = 'Allocate grade';
$string['collaborate:gradesubmission'] = 'Grade submission';
$string['currentgrade'] = 'Current grade: ';
$string['submissiongraded'] = 'Submission graded';
$string['gradingheader'] = 'Grade a submission';
$string['grade'] = 'Grade';

// Scheduled Task.
$string['exportall'] = 'Export all';

// Adhoc task.
$string['namechanged'] = 'Collaborate \'{$a->id}\'s name has changed to \'{$a->name}\'.';

// Events
$string['submission_submitted'] = 'Submission submitted';
$string['submission_graded'] = 'Submission graded';