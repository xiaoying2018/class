<?php
namespace Common\Model;

use Think\Model\RelationModel;

class MxModel extends RelationModel
{
    protected $dbName               =   'mxcrm';

    protected $tablePrefix          =   'mx_';

    protected static function isAuth ()
    {
        return session('?_student');
    }
}