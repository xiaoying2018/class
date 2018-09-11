<?php
namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

abstract class EModel extends RelationModel
{
    protected $dbName               =   'education';

    protected $tablePrefix          =   null;

    protected $_scope               =   [
        'valid'         =>  [
            'where'         =>  ['status'=>1],
        ],
    ];

}