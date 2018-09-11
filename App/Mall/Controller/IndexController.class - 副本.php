<?php
namespace Mall\Controller;

use Home\Model\CourseBespeakModel;
use Home\Model\CourseModel;
use Home\Model\DocCateModel;
use Home\Model\DocModel;
use Mall\Model\SectionCateModel;
use Think\Controller;

class IndexController extends Controller
{
    // mall 所有班级/课程 列表页数据 (目前是首页)
    public function search()
    {
        $course_model = new SectionCateModel();

        $res = $course_model->getShowCourses();

        $this->ajaxReturn(['result'=>true,'data'=>$res]);
    }

    // 班级详情
    public function banjiDetail()
    {
        $id = I('post.id');

        if (!$id) $this->ajaxReturn(['result'=>false,'msg'=>'缺少关键参数.']);

        $course_model = new SectionCateModel();

        $res = $course_model->getBanjiInfo($id);

        if (!$res) $this->ajaxReturn(['result'=>false,'msg'=>'班级不存在.']);

        $this->ajaxReturn(['result'=>true,'data'=> $res]);
    }

    // 课程详情
    public function courseDetail()
    {
        $id = I('post.id');

        if (!$id) $this->ajaxReturn(['result'=>false,'msg'=>'缺少关键参数.']);

        $course_model = new SectionCateModel();

        $res = $course_model->getCourseInfo($id);

        if (!$res) $this->ajaxReturn(['result'=>false,'msg'=>'课程不存在.']);

        $this->ajaxReturn(['result'=>true,'data'=> $res]);
    }

    // 资料数据
    public function getDocList()
    {
        // 实例化模型
        $doc_model = new DocModel();

        // 获取资料
        $doc_list = $doc_model->getMates();

        $this->ajaxReturn(['result'=>true,'data'=>$doc_list]);
    }

    // 资料分类
    public function getDocCate()
    {
        
        // 实例化模型
        $cate_model = new DocCateModel();

        // 获取资料分类列表
        $cates = $cate_model->getCates();

        $this->ajaxReturn(['result'=>true,'data'=>$cates]);
    }

    // FreeClass 公开课 频道页数据
    public function getOpenCourse()
    {
        $course_model = new SectionCateModel();

        $res = $course_model->getOpenCourse();

        $this->ajaxReturn(['result'=>true,'data'=>$res]);
    }

    // xiaonei 校内考 频道页数据
    public function getXiaoneiCourse()
    {
        $course_model = new SectionCateModel();

        $res = $course_model->getXiaoneiCourse();

        $this->ajaxReturn(['result'=>true,'data'=>$res]);
    }

    // zhibo 直播课 频道页数据
    public function getZhiboCourse()
    {
        $course_model = new SectionCateModel();

        $res = $course_model->getZhiboCourse();

        $this->ajaxReturn(['result'=>true,'data'=>$res]);
    }

    // baolu 保录班 频道页数据
    public function getBaoluBan()
    {
        $course_model = new SectionCateModel();

        $res = $course_model->getBaoluBan();

        $this->ajaxReturn(['result'=>true,'data'=>$res]);
    }
    
}