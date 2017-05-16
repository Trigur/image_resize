<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('magickCrop'))
{
    function magickCrop($imgPath = '', $width = null, $height = null, $quality = 70, $type = 'center', $replace = false){
        $ci = & get_instance();

        $ci->image_resize->_setConfig($imgPath, $width, $height, $quality, $type = 'imagickCrop', $replace);
        return $ci->image_resize->_magickCrop($type);
    }
}

if ( ! function_exists('magickScale'))
{
    function magickScale($imgPath = '', $width = null, $height = null, $quality = 70, $replace = false){
        $ci = & get_instance();

        $ci->image_resize->_setConfig($imgPath, $width, $height, $quality, $type = 'imagickScale', $replace);
        return $ci->image_resize->_magickScale($type);
    }
}