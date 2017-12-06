<?php
/**
 * +----------------------------------------------------------------------
 * | 数组转对象动态配置
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.sunnyos.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Date：2017-12-06 08:21:46
 * | Author: Sunny (admin@sunnyos.com) QQ：327388905
 * +----------------------------------------------------------------------
 */

namespace Component\Spl;


class SplArray extends \ArrayObject{
    // 实现get方法
    public function __get($name){
        if(!isset($this[$name])){
            return null;
        }
        return $this[$name];
    }

    // 返回数组的一个副本。 当ArrayObject引用一个对象时，将返回该对象的公共属性的一个数组。
    public function getArrayCopy(){
        $all = parent::getArrayCopy();
        print_r($all);die;
        foreach ($all as $key => $item){
            if($item instanceof SplArray){
                $all[$key] = $item->getArrayCopy();
            }
        }
        return $all;
    }

    // 实现set方法
    public function __set($name, $value){
        $this[$name] = $value;
    }

    public function set($path,$value){
        // 解析路径得到数组
        $path = explode(".",$path);
        $temp = $this;//把当前对象赋值给temp临时变量
        // 循环遍历路径
        while ($key = array_shift($path)){
            // 引用传值得到实际位置
            $temp = &$temp[$key];
        }
        $temp = $value;
    }

    public function get($path,$security = false){
        $paths = explode(".",$path);
        // 使用匿名函数，并且引用全局的$func，每次改变的都是同一个实现递归
        $func = function ($data,$pathArr,$security = false)use(&$func){
            $path = array_shift($pathArr);
            if($path == "*"){
                if($security){
                    if(isset($data['*'])){
                        return $data["*"];
                    }
                }
                if(!empty($pathArr)){
                    $temp = [];
                    foreach ($data as $key => $item){
                        if(is_array($item) && !empty($item)){
                            $temp[$key] = $func($item,$pathArr,$security);
                        }
                        //对于非数组无下级则不再搜索
                    }
                    return $temp;
                }else{
                    return $data;
                }
            }else{
                if(isset($data[$path])){
                    if(!empty($pathArr)){
                        //继续搜索。
                        return $func($data[$path],$pathArr,$security);
                    }else{
                        return $data[$path];
                    }
                }else{
                    return null;
                }
            }
        };
        
        return $func($this->getArrayCopy(),$paths,$security);
    }

    // 实现生产字符串方法
    publci function __toString(){
        return json_encode($this,JSON_UNESCAPED_UNICODE,JSON_UNESCAPED_SLASHES);
    }
}