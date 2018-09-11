<?php
namespace Apply\Model;


class MaterialsApplyModel extends CRMBaseModel
{
    protected $tableName = 'materials_apply';

    public function getMyApply($condition)
    {
        $data = $this->db(1,"DB_CONFIG_EDU")->query('select * from `mx_materials_apply` WHERE '.$condition);
        return $data;
    }

    public function getMaterials($condition)
    {
        $data = $this->db(1,"DB_CONFIG_EDU")->query('select `project_name`,`status` from `mx_materials_apply` WHERE '.$condition.' limit 1')[0];
        return $data;
    }

}