<?php
namespace Studying\Controller;

use Home\Model\CourseBespeakModel;
use Think\Controller;

class ViewController extends Controller
{
    public function index()
    {
        $this->display();
    }
}