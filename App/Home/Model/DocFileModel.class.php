<?php
/**
 * Created by PhpStorm.
 * User: dragon
 * Date: 2018/9/10
 * Time: 14:29
 */

namespace Home\Model;

use Think\Model;

class DocFileModel extends Model
{
    protected $dbName = 'resources';

    protected $tablePrefix = '';

    protected $tableName = 'files';
}