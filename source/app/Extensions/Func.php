<?php namespace YCMS\Extensions;
/**
 * Created by PhpStorm.
 * User: Mo
 * Date: 15-7-6
 * Time: 下午2:35
 */

  class Func {
      public static function make($name,$callback){
          if(!function_exists($name) && is_callable($callback)){
              call_user_func($callback);
          }
      }
  }