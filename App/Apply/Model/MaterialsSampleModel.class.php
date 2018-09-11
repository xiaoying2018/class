<?php
namespace Apply\Model;


class MaterialsSampleModel extends CRMBaseModel
{
    protected $tableName = 'materials_sample';

    public function getMySample($condition)
    {
        $data = $this->db(1,"DB_CONFIG_EDU")->query('select * from `mx_materials_sample` WHERE '.$condition);
        return $data;
    }

}