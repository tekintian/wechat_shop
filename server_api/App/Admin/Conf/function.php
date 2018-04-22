<?php

/**
 * @Author: Tekin
 * @Date:   2018-04-22 15:49:45
 * @Last Modified 2018-04-22
 */
if ( ! function_exists('pw_encrypt')) {
    /**
     * password hash
     *
     * @param      <type>  $str    The string
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    function pw_encrypt($str){
       return md5(hash('sha512',$str).'TEKIN_WXSHOP');
    }
}