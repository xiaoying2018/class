<?php
namespace Home\Model;

use Common\Model\EModel;
use Think\Model\RelationModel;

class StudentModel extends RelationModel
{
    protected $tableName            =   'students';

    protected $_link                =   [
        'letters'           =>  [
            'mapping_order'     =>  'create_at desc',
            'mapping_type'      =>  self::HAS_MANY,
            'class_name'        =>  'Letter',
            'foreign_key'       =>  'student_id',
            'condition'         =>  'status=0',
        ],
        'profile'           =>  [
            'mapping_type'      =>  self::HAS_ONE,
            'class_name'        =>  'Profile',
            'foreign_key'       =>  'student_id',
        ],
    ];

    public function studentInfo ($studentId)
    {
        return $this->relation(true)->find($studentId);
    }

    public function studentSchedule ($studentId)
    {
        // TODO student -> period_student -> [ course_period, course_schedule ] -> [ course, course_section ]

        $_where         =   [
            's.id'              =>  ['eq',$studentId],
            'c.status'          =>  ['eq',1],
            'c_per.status'      =>  ['eq',1],
        ];
        $_field         =   "c.name course_name,c_per.name period_name,c_sec.name section_name,c_sec.id section_id,c_sch.id schedule_id,c_sch.period_id,c_sch.serial,
        mxu1.full_name headmaster,mxu2.full_name teacher,c_sec.title,c_sch.start_time,c_sec.duration,
        date_add(c_sch.start_time,INTERVAL c_sec.duration MINUTE) end_time,UNIX_TIMESTAMP(c_sch.start_time) stamp,s_in.status signin_status,m.path homework";
        return $this->alias('s')->field($_field)
            ->join("LEFT JOIN __PERIOD_STUDENT__ p_s ON s.id = p_s.student_id")
            ->join("LEFT JOIN __COURSE_PERIOD__ c_per ON p_s.period_id = c_per.id")
            ->join("LEFT JOIN __COURSE_SCHEDULE__ c_sch ON p_s.period_id = c_sch.period_id")
            ->join("LEFT JOIN __COURSE__ c ON c.id = c_per.course_id")
            ->join("LEFT JOIN __COURSE_SECTION__ c_sec ON c_sec.id = c_sch.section_id")
            ->join("LEFT JOIN __SCHEDULE_SIGNIN__ s_in ON c_sch.id = s_in.schedule_id")
            ->join("LEFT JOIN mxcrm.mx_user mxu1 ON mxu1.user_id = c_per.headmaster_id")
            ->join("LEFT JOIN mxcrm.mx_user mxu2 ON mxu2.user_id = c_sch.teacher_id")
            ->join("LEFT JOIN education.material m ON m.id = c_sch.homework")
            ->where($_where)
            ->group('c_sch.id')
            ->select();
    }

    public function student_login ($username,$password)
    {
        $userKey                =   static::username($username);
        $where                  =   [
            $userKey        =>  ['eq',$username],
        ];
        $studentInfo            =   $this->field('id,code,realname,mobile,email,password')
            ->where( $where )
            ->find();

        return ( $studentInfo
            && $studentInfo[$userKey] === $username
            && password_verify($password, $studentInfo['password']) )
            ?   $studentInfo
            :   false;
    }

    public static function username( $username )
    {
        // 学号匹配
        if( pattern( "/^[2-9][0-9]{20,23}$/", $username ) ){
            return 'code';
        }elseif( pattern('/^([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?$/i', $username) ){
            return 'email';
        }else{
            return 'mobile';
        }
    }
}