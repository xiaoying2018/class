<?php
namespace Apply\Model;


class MaterialsModel extends CRMBaseModel
{
    protected $tableName = 'materials';

    public function getMaterials($condition)
    {
        $data = $this->db(1,"DB_CONFIG_EDU")->query('select * from `mx_materials` WHERE '.$condition.' order by `create_time` desc');
        return $data;
    }

    public function findMyMaterials($id)
    {
        $data = $this->db(1,"DB_CONFIG_EDU")->query('select * from `mx_materials` WHERE id = '.$id.' limit 1');
        return $data?$data[0]:[];
    }

    public function delOne($id)
    {
        return $this->db(1,"DB_CONFIG_EDU")->execute('delete from `mx_materials` WHERE id = '.$id);
    }

    public function addMaterials($data)
    {
        return $this->db(1,"DB_CONFIG_EDU")->execute('insert into `mx_materials` (`name`,`cate_id`,`program_id`,`student_id`,`create_time`,`file`,`sname`,`program_name`) values ("'.$data['name'].'",'.$data['cate_id'].','.$data['program_id'].','.$data['student_id'].','.$data['create_time'].',"'.$data['file'].'","'.$data['sname'].'","'.$data['program_name'].'")');
    }

}