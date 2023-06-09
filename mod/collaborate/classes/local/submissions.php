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
 * Class for handling student submissions.
 *
 * @package   mod_collaborate
 * @copyright 2018 Richard Jones https://richardnz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_collaborate\local;
use \mod_collaborate\local\collaborate_editor;
use \mod_collaborate\local\debugging;
use \mod_collaborate\local\submission_form;
use core\dataformat;

defined('MOODLE_INTERNAL') || die();

class submissions {
    /**
     * Add a submission record to the DB.
     *
     * @param object $data - the data to add
     * @param object $context - our module context
     * @param int $cid our collaborate instance id.
     * @return int $id - the id of the inserted record
     */
    public static function save_submission($data, $context, $cid, $page) {
        global $DB, $USER;
        $exists = self::get_submission($cid, $USER->id, $page);
        if($exists) {
            $data->timemodified = time();
            $data->id = $exists->id;
        }else{
            // Insert a dummy record and get the id.
            $data->timecreated = time();
            $data->timemodified = time();
            $data->collaborateid = $cid;
            $data->userid = $USER->id;
            $data->page = $page;
            $data->submission = ' ';
            $data->submissionformat = FORMAT_HTML;
            $dataid = $DB->insert_record('collaborate_submissions', $data);
            $data->id = $dataid;
        }

        $options = collaborate_editor::get_editor_options($context);

        // Massage the data into a form for saving.
        $data = file_postupdate_standard_editor(
            $data,
            'submission',
            $options,
            $context,
            'mod_collaborate',
            'submission',
            $data->id
        );

        // Update the record with full editor data.
        $DB->update_record('collaborate_submissions', $data);
        return $data->id;
    }
    /**
     * Retrieve a submission record from the DB.
     *
     * @param int $cid Our collaborate instance id.
     * @param int $userid The user making the submission.
     * @param int $page The page identifier (a or b).
     * @return object Representing the record or null if it doesn't exist.
     */
    public static function get_submission($cid, $userid, $page) {
        global $DB;
        return $DB->get_record(
            'collaborate_submissions',
            ['collaborateid' => $cid, 'userid' => $userid, 'page' => $page],
            '*',
            IGNORE_MISSING
        );
    }

    /**
     * Retrieve a submission record for grading.
     *
     * @param object $collaborate Our collaborate instance.
     * @param int $sid The submission id.
     * @return object $data The data required for the grading form.
     */
    public static function get_submission_to_grade($collaborate, $sid) {
        global $DB;

        $record = $DB->get_record('collaborate_submissions', ['id' => $sid], '*', MUST_EXIST);
        $data = new \stdClass();
        $data->title = $collaborate->title;
        $data->submission = $record->submission;

        $user = $DB->get_record('user', ['id' => $record->userid], '*', MUST_EXIST);
        $data->name = $user->firstname.' '.$user->lastname;
        $data->grade = (is_null($record->grade)) ? '-' : $record->grade;  // So that '-' is shown when first not graded.

        return $data;
    }

    /**
     * Update a submission grade.
     *
     * @param int $sid The submission id.
     * @param int $grade The submission grade.
     * @return none.
     */

    public static function update_grade($sid, $grade) {
        global $DB;
        $DB->set_field('collaborate_submissions', 'grade', $grade, ['id' => $sid]);
    }
    
    /**
     * The highest grade the user.
     *
     * @param int $sid The submission id.
     * @return int The highest grade the user achieved if there are multiple submissions by the same user.
     */
    public static function grade_user($attempts) {
        // We could use different strategies here.
        $maxscore = 0;
        foreach ($attempts as $attempt) {
            $grade = $attempt->grade;
            $maxscore = ($grade > $maxscore) ? $grade : $maxscore;
        }
        return $maxscore;
    }

    /**
     *  Get the student submission records to be saved to a file.
     *
     * @param object $collaborate The Collaborate instance cotaining submissions
     * @param object $context The module context.
     * @return array of objects $records The records to be exported.
     */
    public static function get_export_data($collaborate, $context) {
        global $DB;

        $sql = "SELECT s.id, u.firstname, u.lastname, s.submission,  s.grade
                FROM {collaborate_submissions} AS s
                JOIN {collaborate} AS c ON s.collaborateid = c.id
                JOIN {user} AS u ON s.userid = u.id
                WHERE u.id <> 0
                AND s.collaborateid = :cid";

        $records = $DB->get_records_sql($sql, ['cid' => $collaborate->id]);

        // Process the submissions
        foreach ($records as $record) {
            $content = file_rewrite_pluginfile_urls($record->submission, 'pluginfile.php',
                $context->id,'mod_collaborate', 'submission', $record->id);

            // Format submission.
            $formatoptions = new \stdClass;
            $formatoptions->noclean = true;
            $formatoptions->overflowdiv = true;
            $formatoptions->context = $context;

            $record->submission = format_text($content, FORMAT_HTML, $formatoptions);
        }
        return $records;
    }

    /**
     *  Get the column headers for the export file.
     *
     * @return Array array.
     */
    public static function get_export_headers() {
        return [
            get_string('id', 'mod_collaborate'),
            get_string('firstname', 'core'),
            get_string('lastname', 'core'),
            get_string('submission','mod_collaborate'),
            get_string('grade', 'grades')
        ];
    }
    /**
     *  Export all submissions of all Collaborate instances.
     *
     * @return none.
     */
    public static function export_all_submissions() {
        global $CFG, $DB;

        // Get the all Collaborate instances.

        $sql = "SELECT s.id, u.firstname, u.lastname, s.submission,  s.grade,
                c.id AS cid, c.course
                FROM {collaborate_submissions} AS s
                JOIN {collaborate} AS c ON s.collaborateid = c.id
                JOIN {user} AS u ON s.userid = u.id
                WHERE u.id <> 0";

        $records = $DB->get_records_sql($sql);
        $submissions = array();

        // Locate the corresponding entries in the submissions table.
        foreach ($records as $record) {
            $data = array();

            // Get the correct context for pluginfiles and formatting.
            $courseid = $record->course;
            $cid = $record->cid;
            $cm = get_coursemodule_from_instance('collaborate', $cid, $courseid, false, MUST_EXIST);
            $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
            $context = \context_module::instance($cm->id);

            // Extract the wanted data.
            $data['id'] = $record->id;
            $data['firstname'] = $record->firstname;
            $data['lastname'] = $record->lastname;

            // Process media files (for printing).
            $content = \file_rewrite_pluginfile_urls($record->submission, 'pluginfile.php', $context->id,'mod_collaborate', 'submission', $record->id);

            // Format submission.
            $formatoptions = new \stdClass;
            $formatoptions->noclean = true;
            $formatoptions->overflowdiv = true;
            $formatoptions->context = $context;

            $data['submission'] = \format_text($content, FORMAT_HTML, $formatoptions);
            $data['grade'] = $record->grade;
            $submissions[] = $data;
        }

        // Export the submissions to a pdf file.
        $fields = self::get_export_headers();
        $download_submissions = new \ArrayObject($submissions);
        $iterator = $download_submissions->getIterator();
        $dataformat = 'pdf';
        $filename = clean_filename('export_submissions_' . time());
        $exportfile = dataformat::write_data($filename, $dataformat, $fields, $iterator);

        // Move the file from a temporary location to one that we know about before its deleted.
        rename($exportfile, $CFG->dataroot.'/'.$filename.'.pdf');
        
    }
}