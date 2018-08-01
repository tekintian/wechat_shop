<?php

/**
 * @Author: Tekin
 * @Date:   2018-04-22 16:37:10
 * @Last Modified 2018-04-22* @Last Modified time: 2018-04-22 16:37:10
 */

if ( ! function_exists('pp')) {
    /**
     * 格式输出
     *
     * @param      <type>  $arr    The arr
     */
    function pp($arr)
    {
        if (is_array($arr)) {
            echo "<pre>";
            print_r($arr);
            echo "</pre>";
        } else if (is_object($arr)) {
            echo "<pre>";
            print_r($arr);
            echo "</pre>";

        } else {
            echo $arr;
        }
        die;
    }
}

if ( ! function_exists('pr')) {
    /**
     * 打印不中断
    */
    function pr($arr) {
        if (is_array($arr)) {
                echo "<pre>";
                print_r($arr);
                echo "</pre>";
            } else if (is_object($arr)) {
                echo "<pre>";
                print_r($arr);
                echo "</pre>";

            } else {
                echo $arr;
            }
        }
}

if ( ! function_exists('vd')) {
    /**
     * 测试打印函数
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    function vd($arr)
    {
        echo "<pre>";
        var_dump($arr);
        echo "</pre>";
        die;
    }
}
