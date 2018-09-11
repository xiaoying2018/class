<?php
namespace Plus\Controller;

use Think\Controller;

class ViewController extends Controller
{
    public function index ()
    {
        $this->display('View_index');
    }
    public function join ()
    {
        $this->display('View_join');
    }
    public function experience ()
    {
        $this->display('View_experience');
    }
}