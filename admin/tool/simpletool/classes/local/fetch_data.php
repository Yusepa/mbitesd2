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
 * tool_simpletool main file
 *
 * @package   tool_simpletool
 * @copyright  Daniel Neis <danielneis@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Modified for use in MoodleBites for Developers Level 1
 * by Richard Jones & Justin Hunt.
 *
 * See: https://www.moodlebites.com/mod/page/view.php?id=24546
 */

namespace tool_simpletool\local;

/**
 * Class to fetch data from the database.
 *
 * @package tool_simpletool
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://moodle.org/mod/forum/discuss.php?d=330687
 */
class fetch_data {

    public static function user_data() {
        global $DB;

        return $DB->get_records('user', ['deleted' => 0], '', 'username, firstname, lastname');
    }

    public static function resource_data() {
        global $DB;

        $sql = "SELECT COUNT(l.id) COUNT, l.course, c.fullname coursename 
                FROM {resource} l 
                INNER JOIN {course} c ON l.course = c.id 
                GROUP BY course 
                ORDER BY COUNT DESC";

        return $DB->get_records_sql($sql);
    }

    public static function block_data() {
        global $DB;
        $sql = "SELECT b.id, cat.id AS catid, cat.name AS catname, b.blockname, c.shortname 
                FROM {context} x 
                JOIN {block_instances} b ON b.parentcontextid = x.id 
                JOIN {course} c ON c.id = x.instanceid 
                JOIN {course_categories} cat ON cat.id = c.category 
                WHERE x.contextlevel <= :clevel 
                ORDER BY b.blockname DESC";

        return $DB->get_records_sql($sql, ['clevel' => 80]);
    }

    public static function collaborate_submission_data($sorting) {
        global $DB;

        switch ($sorting) {
            case 'name' : $sorting = 'c.name';
            break;
            case 'title' : $sorting = 'c.title';
            break;
            case 'firstname' : $sorting = 'u.firstname';
            break;
            case 'lastname' : $sorting = 'u.lastname';
            break;
        }

        $sql = "SELECT s.id, s.collaborateid, s.page, s.userid, s.submission,
                    c.name, c.title, c.course, co.fullname AS 'coursename',
                    u.firstname, u.lastname
                FROM {collaborate_submissions} s
                JOIN {collaborate} c ON s.collaborateid = c.id
                JOIN {user} u ON s.userid = u.id
                JOIN {course} co ON co.id = c.course
                WHERE u.deleted = :deleted
                ORDER BY $sorting ASC";

        return $DB->get_records_sql($sql, ['deleted' => 0]);
    }
}
