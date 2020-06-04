<?php

namespace app\admin\controller;

use think\Controller;

class File extends Controller
{
    /**
     * 上传单文件
     */
    public function upload_single()
    {
        $file = $this->request->file('image');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $siteHost = '/uploads/';
                $path = str_replace("\\","/",$info->getSaveName());
                $fullPath = $siteHost.$path;
                $data = [
                    'path' => $path,
                    'full_path' => $fullPath
                ];
                return ['code'=>1,'message'=>'上传成功', 'data'=>$data];
            }else{
                // 上传失败获取错误信息
                return ['code'=>0,'message'=>$file->getError()];
            }
        } else {
            return ['code'=>0,'message'=>'没有文件上传'];
        }
    }
}
