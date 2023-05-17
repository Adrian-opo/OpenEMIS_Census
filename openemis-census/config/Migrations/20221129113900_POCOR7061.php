<?php
use Migrations\AbstractMigration;

class POCOR7061 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        // Backup table
        $this->execute('CREATE TABLE `zz_7061_report_queries` LIKE `report_queries`');
        $this->execute('INSERT INTO `zz_7061_report_queries` SELECT * FROM `report_queries`');


        // DROP existing report_student_attendance_summary table since the new table will have new table strcture (addition of new columns)
        $this->execute('DROP TABLE IF EXISTS `report_student_attendance_summary`');

        
        // CREATE summary tables and INSERT new rows into report_queries table
        $this->execute('CREATE TABLE IF NOT EXISTS `report_student_attendance_summary`( `academic_period_id` int(10) DEFAULT NULL, `academic_period_name` varchar(150) DEFAULT NULL, `institution_id` int(10) DEFAULT NULL, `institution_code` varchar(150) DEFAULT NULL, `institution_name` varchar(150) DEFAULT NULL, `education_grade_id` int(10) DEFAULT NULL, `education_grade_code` varchar(150) DEFAULT NULL, `education_grade_name` varchar(150) DEFAULT NULL, `class_id` int(10) DEFAULT NULL, `class_name` varchar(150) DEFAULT NULL, `attendance_date` date DEFAULT NULL, `period_id` int(10) DEFAULT NULL, `period_name` varchar(70) DEFAULT NULL, `subject_id` int(10) DEFAULT NULL, `subject_name` varchar(150) DEFAULT NULL, `female_count` int(10) DEFAULT NULL, `male_count` int(10) DEFAULT NULL, `total_count` int(10) DEFAULT NULL, `marked_attendance` int(10) DEFAULT NULL, `unmarked_attendance` int(10) DEFAULT NULL, `present_female_count` int(10) DEFAULT NULL, `present_male_count` int(10) DEFAULT NULL, `present_total_count` int(10) DEFAULT NULL, `absent_female_count` int(10) DEFAULT NULL, `absent_male_count` int(10) DEFAULT NULL, `absent_total_count` int(10) DEFAULT NULL, `late_female_count` int(10) DEFAULT NULL, `late_male_count` int(10) DEFAULT NULL, `late_total_count` int(10) DEFAULT NULL, `created` datetime NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->execute('INSERT INTO `report_queries` (`name`, `query_sql`, `frequency`, `status`, `modified_user_id`, `modified`, `created_user_id`, `created`) VALUES ("report_student_attendance_summary_truncate", "TRUNCATE report_student_attendance_summary;", "week", 1, NULL, NULL, 1, CURRENT_TIMESTAMP)');
        $this->execute('INSERT INTO `report_queries` (`id`, `name`, `query_sql`, `frequency`, `status`, `modified_user_id`, `modified`, `created_user_id`, `created`) VALUES (NULL, "report_student_attendance_summary_insert", "INSERT INTO report_student_attendance_summary(academic_period_id, academic_period_name, institution_id, institution_code, institution_name, education_grade_id, education_grade_code, education_grade_name, class_id, class_name, attendance_date, period_id, period_name, subject_id, subject_name, female_count, male_count, total_count, marked_attendance, unmarked_attendance, present_female_count, present_male_count, present_total_count, absent_female_count, absent_male_count, absent_total_count, late_female_count, late_male_count, late_total_count, created) SELECT total_students_data.academic_period_id ,total_students_data.academic_period_name ,total_students_data.institution_id ,total_students_data.institution_code ,total_students_data.institution_name ,total_students_data.education_grade_id ,total_students_data.education_grade_code ,total_students_data.education_grade_name ,total_students_data.institution_class_id ,total_students_data.institution_class_name ,all_dates.selected_date ,IFNULL(count_periods.period_id, 1) period_id ,count_periods.period_name ,IFNULL(subjects_info.institution_subject_id, 0) institution_subject_id ,subjects_info.institution_subject_name ,total_students_data.female_students ,total_students_data.male_students ,total_students_data.total_students ,IF(attendance_data.academic_period_id IS NOT NULL, total_students_data.total_students, 0) marked_attendance ,IF(attendance_data.academic_period_id IS NULL, total_students_data.total_students, 0) unmarked_attendance ,IF(attendance_data.academic_period_id IS NULL, 0, total_students_data.female_students - IFNULL(absence_data.total_absent_female, 0) - IFNULL(absence_data.total_late_female, 0)) female_present_students ,IF(attendance_data.academic_period_id IS NULL, 0, total_students_data.male_students - IFNULL(absence_data.total_absent_male, 0) - IFNULL(absence_data.total_late_male, 0)) male_present_students ,IF(attendance_data.academic_period_id IS NULL, 0, total_students_data.total_students - IFNULL(absence_data.total_absent, 0) - IFNULL(absence_data.total_late, 0)) total_present_students ,IFNULL(absence_data.total_absent_female, 0) total_absent_female ,IFNULL(absence_data.total_absent_male, 0) total_absent_male ,IFNULL(absence_data.total_absent, 0) total_absent ,IFNULL(absence_data.total_late_female, 0) total_late_female ,IFNULL(absence_data.total_late_male, 0) total_late_male ,IFNULL(absence_data.total_late, 0) total_late ,CURRENT_TIMESTAMP created FROM ( SELECT academic_periods.id academic_period_id ,academic_periods.name academic_period_name ,institutions.id institution_id ,institutions.code institution_code ,institutions.name institution_name ,education_grades.id education_grade_id ,education_grades.code education_grade_code ,education_grades.name education_grade_name ,institution_classes.id institution_class_id ,institution_classes.name institution_class_name ,SUM(CASE WHEN security_users.gender_id IN (1, 2) THEN 1 ELSE 0 END) total_students ,SUM(CASE WHEN security_users.gender_id = 2 THEN 1 ELSE 0 END) female_students ,SUM(CASE WHEN security_users.gender_id = 1 THEN 1 ELSE 0 END) male_students ,academic_periods.start_date ,academic_periods.end_date FROM institution_class_students INNER JOIN security_users ON security_users.id = institution_class_students.student_id INNER JOIN institutions ON institutions.id = institution_class_students.institution_id INNER JOIN education_grades ON education_grades.id = institution_class_students.education_grade_id INNER JOIN institution_classes ON institution_classes.id = institution_class_students.institution_class_id AND institution_classes.institution_id = institution_class_students.institution_id AND institution_classes.academic_period_id = institution_class_students.academic_period_id INNER JOIN academic_periods ON academic_periods.id = institution_class_students.academic_period_id WHERE IF((CURRENT_DATE >= academic_periods.start_date AND CURRENT_DATE <= academic_periods.end_date), institution_class_students.student_status_id = 1, institution_class_students.student_status_id IN (1, 7, 6, 8)) GROUP BY institution_classes.id) total_students_data INNER JOIN ( SELECT date_generator.selected_date FROM ( SELECT adddate(\"1970-01-01\",t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date FROM (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0, (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1, (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2, (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3, (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4 ) date_generator INNER JOIN ( SELECT MIN(academic_periods.start_date) min_date ,CURDATE() max_date FROM institution_students INNER JOIN academic_periods ON academic_periods.id = institution_students.academic_period_id ) date_ranges WHERE selected_date BETWEEN date_ranges.min_date AND date_ranges.max_date ) all_dates ON all_dates.selected_date BETWEEN total_students_data.start_date AND total_students_data.end_date LEFT JOIN ( SELECT student_mark_type_status_grades.education_grade_id ,student_mark_type_statuses.academic_period_id ,student_attendance_per_day_periods.id period_id ,student_attendance_per_day_periods.name period_name FROM student_mark_type_status_grades INNER JOIN student_mark_type_statuses ON student_mark_type_statuses.id = student_mark_type_status_grades.student_mark_type_status_id INNER JOIN student_attendance_mark_types ON student_attendance_mark_types.id = student_mark_type_statuses.student_attendance_mark_type_id LEFT JOIN student_attendance_per_day_periods ON student_attendance_per_day_periods.student_attendance_mark_type_id = student_attendance_mark_types.id INNER JOIN student_attendance_types ON student_attendance_types.id = student_attendance_mark_types.student_attendance_type_id ) count_periods ON count_periods.education_grade_id = total_students_data.education_grade_id AND count_periods.academic_period_id = total_students_data.academic_period_id LEFT JOIN ( SELECT institution_class_subjects.institution_class_id ,institution_subjects.id institution_subject_id ,institution_subjects.name institution_subject_name FROM institution_class_subjects INNER JOIN institution_subjects ON institution_subjects.id = institution_class_subjects.institution_subject_id ) subjects_info ON subjects_info.institution_class_id = total_students_data.institution_class_id AND count_periods.period_name IS NULL LEFT JOIN ( SELECT student_attendance_marked_records.academic_period_id ,student_attendance_marked_records.institution_id ,student_attendance_marked_records.education_grade_id ,student_attendance_marked_records.institution_class_id ,student_attendance_marked_records.date ,student_attendance_marked_records.period periods_presence_marked_id ,student_attendance_marked_records.subject_id subjects_presence_marked_id FROM student_attendance_marked_records GROUP BY student_attendance_marked_records.academic_period_id ,student_attendance_marked_records.institution_id ,student_attendance_marked_records.education_grade_id ,student_attendance_marked_records.institution_class_id ,student_attendance_marked_records.date ,student_attendance_marked_records.period ,student_attendance_marked_records.subject_id ) attendance_data ON attendance_data.academic_period_id = total_students_data.academic_period_id AND attendance_data.institution_id = total_students_data.institution_id AND attendance_data.education_grade_id = total_students_data.education_grade_id AND attendance_data.institution_class_id = total_students_data.institution_class_id AND attendance_data.date = all_dates.selected_date AND attendance_data.periods_presence_marked_id = IFNULL(count_periods.period_id, 1) AND attendance_data.subjects_presence_marked_id = IFNULL(subjects_info.institution_subject_id, 0) LEFT JOIN ( SELECT institution_student_absence_details.academic_period_id ,institution_student_absence_details.institution_id ,institution_student_absence_details.education_grade_id ,institution_student_absence_details.institution_class_id ,institution_student_absence_details.date ,institution_student_absence_details.period periods_absence_marked_id ,institution_student_absence_details.subject_id subjects_absence_marked_id ,SUM(CASE WHEN security_users.gender_id IN (1, 2) AND institution_student_absence_details.absence_type_id IN (1,2) THEN 1 ELSE 0 END) total_absent ,SUM(CASE WHEN security_users.gender_id = 2 AND institution_student_absence_details.absence_type_id IN (1,2) THEN 1 ELSE 0 END) total_absent_female ,SUM(CASE WHEN security_users.gender_id = 1 AND institution_student_absence_details.absence_type_id IN (1,2) THEN 1 ELSE 0 END) total_absent_male ,SUM(CASE WHEN security_users.gender_id IN (1, 2) AND institution_student_absence_details.absence_type_id = 3 THEN 1 ELSE 0 END) total_late ,SUM(CASE WHEN security_users.gender_id = 2 AND institution_student_absence_details.absence_type_id = 3 THEN 1 ELSE 0 END) total_late_female ,SUM(CASE WHEN security_users.gender_id = 1 AND institution_student_absence_details.absence_type_id = 3 THEN 1 ELSE 0 END) total_late_male FROM institution_student_absence_details INNER JOIN institution_class_students ON institution_student_absence_details.student_id = institution_class_students.student_id AND institution_student_absence_details.institution_class_id = institution_class_students.institution_class_id INNER JOIN security_users ON security_users.id = institution_student_absence_details.student_id GROUP BY institution_student_absence_details.academic_period_id ,institution_student_absence_details.institution_id ,institution_student_absence_details.education_grade_id ,institution_student_absence_details.institution_class_id ,institution_student_absence_details.date ,institution_student_absence_details.period ,institution_student_absence_details.subject_id ) absence_data ON absence_data.academic_period_id = total_students_data.academic_period_id AND absence_data.institution_id = total_students_data.institution_id AND absence_data.education_grade_id = total_students_data.education_grade_id AND absence_data.institution_class_id = total_students_data.institution_class_id AND absence_data.date = all_dates.selected_date AND absence_data.periods_absence_marked_id = IFNULL(count_periods.period_id, 1) AND absence_data.subjects_absence_marked_id = IFNULL(subjects_info.institution_subject_id, 0)", "week", "1", NULL, NULL, "1", CURRENT_TIMESTAMP)');
    
    }
         
    // rollback
    public function down()
    {
        // Restore table
        $this->execute('DROP TABLE IF EXISTS `report_queries`');
        $this->execute('RENAME TABLE `zz_7061_report_queries` TO `report_queries`');

        // Drop summary tables
        $this->execute('DROP TABLE IF EXISTS `report_student_attendance_summary`');

    }
}