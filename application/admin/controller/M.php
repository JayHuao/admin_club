<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Config;
use think\Db;
use think\File;

class M extends AdminBase
{

    /**
     * 上传图片
     */
    public function upload()
    {
        $file = request()->file('image');
        $fullPath = upload_single($file);

        return ['code'=>1,'message'=>'上传成功', 'data'=>$fullPath];
    }

}
