<?php
namespace Home\Model;

use Common\Model\EModel;

class PeriodModel   extends EModel
{
    protected $tableName                =   'course_period';

    public function period_list ($where=null){
        $_where             =   [];
        if( !is_null($_where) && is_array($where) ){
            $_where         =   array_merge( $_where, $where );
        }

        $fields             =   "c_per.name period_name,c.name course_name,c.pic course_pic,mx_u1.full_name headmaster,
        c_sch.start_time,c_per.id period_id,c.id course_id,c_per.headmaster_id,
        count(*) section_total,datediff( max(c_sch.start_time), min(c_sch.start_time) )+1 cycle,
        sum(c_sec.duration) time_long,c.detail course_detail,DATE_FORMAT(min(c_sch.start_time),'%Y年%m月%d日') start_day,
        DATE_FORMAT(max(c_sch.start_time),'%Y年%m月%d日') end_day,SUM( IF(c_sch.start_time < CURRENT_TIMESTAMP(),1,0) ) finished";

        return $this->field($fields)
            ->join("c_per LEFT JOIN {$this->dbName}.period_student p_s ON c_per.id = p_s.period_id")
            ->join("LEFT JOIN {$this->dbName}.students s ON s.id = p_s.student_id")
            ->join("LEFT JOIN {$this->dbName}.course c ON c.id = c_per.course_id")
            ->join("LEFT JOIN {$this->dbName}.course_schedule c_sch ON c_per.id = c_sch.period_id")
            ->join("LEFT JOIN {$this->dbName}.course_section c_sec ON c_sch.section_id = c_sec.id")
            ->join("LEFT JOIN mxcrm.mx_user mx_u1 ON mx_u1.user_id = c_per.headmaster_id")
            ->where($_where)
            ->group('c_sch.period_id')
            ->order('c_sec.node')
            ->select();
    }
}