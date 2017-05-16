<?php
/*
    https://github.com/tim-reynolds/crop/tree/UpdateEntropyAlgorithm

    Copyright (c) 2013, Stig Lindqvist
    All rights reserved.

    Redistribution and use in source and binary forms, with or without modification,
    are permitted provided that the following conditions are met:

      Redistributions of source code must retain the above copyright notice, this
      list of conditions and the following disclaimer.

      Redistributions in binary form must reproduce the above copyright notice, this
      list of conditions and the following disclaimer in the documentation and/or
      other materials provided with the distribution.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
    ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
    ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
    (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
    LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
    ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
    SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

    Автор модуля imageCMS:
    trigur@yandex.ru
*/

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Image_resize extends MY_Controller
{
    private $outerImagesDir = '/uploads/outer';
    private $resizedFolderName = 'resized';

    private $imgPath;
    private $width; 
    private $height;
    private $quality;
    private $type;
    private $replace;

    private $sourceRelativePath;
    private $sourceFullPath;

    private $destinationRelativePath;
    private $destinationFullPath;

    private $fileExists = false;

    public function __construct() {
        parent::__construct();

        $this->load->helper('image_resize');
    }

    /*
        Пропорциональное уменьшение изображение
    */
    public function _magickScale()
    {
        return $this->_start(function(){
            $scale = $this->load->model('imagick/Scale', 'scale');

            $scale->setImagePath($this->sourceFullPath);
            $scaledImage = $scale->resizeAndScale($this->width, $this->height, $this->quality);
            $scaledImage->writeimage($this->destinationFullPath);

            return $this->destinationRelativePath;
        });
    }

    /*
        Обрезание изображения
    */
    public function _magickCrop($type)
    {
        return $this->_start(function() use($type){
            require_once(__DIR__ . '/models/imagick/crop.php');

            switch ($type) {
                case 'entropy':
                    $crop = $this->load->model('imagick/Entropy');
                    break;
                case 'balanced':
                    $crop = $this->load->model('imagick/Balanced');
                    break;
                case 'face':
                    $this->load->model('imagick/Entropy');
                    $crop = $this->load->model('imagick/Face');
                    break;
                
                case 'center':
                default:
                    $crop = $this->load->model('imagick/Center');
                    break;
            }

            $crop->setImagePath($this->sourceFullPath);
            $croppedImage = $crop->resizeAndCrop($this->width, $this->height, $this->quality);
            $croppedImage->writeimage($this->destinationFullPath);

            return $this->destinationRelativePath;
        });
    }

    /*
        Устанавливаем конфиг
    */
    public function _setConfig($imgPath = '', $width = null, $height = null, $quality = 70, $type = 'scale', $replace = false)
    {
        $this->imgPath = $imgPath;
        $this->width   = $width; 
        $this->height  = $height;
        $this->quality = $quality;
        $this->type    = $type;
        $this->replace = $replace;
    }

    /*
        Запускаем процесс ресайза
    */
    private function _start($callback)
    {
        if (ENVIRONMENT == 'development'){
            $this->_init();
        } else{
            try {
                $this->_init();
            } catch (Exception $e) {
                return $this->imgPath;
            }
        }

        if (file_exists($this->destinationFullPath)) {
            return $this->destinationRelativePath;
        }

        return $callback();
    }

    /*
        Инициализация.
        Проверка входных параметров.
        Установка путей исходного и конечного файла.
    */
    private function _init()
    {
        $imgPath = $this->imgPath;
        $width   = $this->width; 
        $height  = $this->height;
        $quality = $this->quality;
        $type    = $this->type;
        $replace = $this->replace;

        if ( empty($imgPath) || (empty($width) && empty($height)) ) {
            throw new Exception("Некорректные входные данные.", 1);
        }

        switch ($type) {
            case 'imagickCrop':
                $this->_checkMagickAvailable();
                $typeMark = '__imc_';
                break;

            case 'imagickScale':
                $this->_checkMagickAvailable();
                $typeMark = '__ims_';
                break;
            
            default:
                throw new Exception("Некорректный тип ресайза.", 1);
                break;
        }

        $widthMark = empty($width) ? 'auto' : $width;
        $heightMark = empty($height) ? 'auto' : $height;

        $mark = $typeMark . $widthMark . 'x' . $heightMark;

        if (self::_pathIsLocal($imgPath)) {

            $this->sourceRelativePath = $this->_getRelativePath($imgPath);
            $this->sourceFullPath = $this->_getDocumentRoot() . $this->sourceRelativePath;

            if (! is_file($this->sourceFullPath)) {
                throw new Exception("Исходное изображение не найдено!", 1); 
            }

            if ($replace) {
                $this->destinationRelativePath = $this->sourceRelativePath;
                $this->destinationFullPath = $this->sourceFullPath;
            } else {
                $this->destinationRelativePath = $this->_makeLocalDestinationPath($imgPath, $mark);
            }
        } else {
            $this->sourceFullPath = $imgPath;
            $this->destinationRelativePath = $this->_makeOuterDestinationPath($imgPath, $mark);
        }

        if (! $replace) {
            $this->destinationFullPath = self::_getDocumentRoot() . $this->destinationRelativePath;
        }
    }

    /*
        Проверка, включен ли модуль imagick
    */
    private function _checkMagickAvailable()
    {
        if (! extension_loaded('imagick')) {
            throw new Exception("Модуль imagick не включен.", 1);
        }
    }

    /*
        Получение относительного пути к файлу
    */
    private function _getRelativePath($imgPath)
    {
        $relativePath = str_replace(self::_getDocumentRoot(), '', $imgPath);

        if (strrpos($relativePath, self::_getDomain()) !== false) {
            $relativePath = explode(self::_getDomain(), $relativePath);
            $relativePath = $relativePath[1];
        }

        return '/' . ltrim($relativePath, '/');
    }

    /*
        Установка пути к конечному файлу при локальном исходном
    */
    private function _makeLocalDestinationPath($imgPath, $mark)
    {
        $pathArray = explode('/', ltrim($imgPath, '/'));
        $imgName = $pathArray[count($pathArray) - 1];
        unset($pathArray[count($pathArray) - 1]);

        $imgNameArray = self::_getExt($imgName);

        if ($imgNameArray['ext']) {
            $imgNameArray['ext'] = '.' . $imgNameArray['ext'];
        }

        $destinationPath = 
            '/' . implode('/', $pathArray) . '/'
            . trim($this->resizedFolderName, '/') . '/'
            . $imgNameArray['name'] . '/';

        $this->_makeDestinationPath($destinationPath);

        $destinationPath .= $imgNameArray['name'] . $mark . $imgNameArray['ext'];

        return $destinationPath;
    }

    /*
        Установка пути к конечному файлу при внешнем исходном
    */
    private function _makeOuterDestinationPath($imgPath, $mark)
    {

        $pathArray = explode('/', $imgPath);
        $imgName = $pathArray[count($pathArray) - 1];

        $imgNameArray = self::_getExt($imgName);

        if ($imgNameArray['ext']) {
            $imgNameArray['ext'] = '.' . $imgNameArray['ext'];
        }

        $destinationPath = 
            '/' . trim($this->outerImagesDir, '/') . '/'
            . $imgNameArray['name'] . '/';

        $this->_makeDestinationPath($destinationPath);

        $destinationPath .= $imgNameArray['name'] . $mark . $imgNameArray['ext'];

        return $destinationPath;
    }

    /*
        Создаем путь
    */
    private static function _makeDestinationPath($path)
    {
        $path = self::_getDocumentRoot() . $path;
        if (! file_exists($path)) {
            if (! @mkdir($path, 0777, $recursive = true)){
                // $mkdirErrorArray = error_get_last();
                throw new Exception("Невозможно создать конечную папку.", 1);
            }
        }
    }

    /*
        Проверяем, находится ли файл у нас на сервере, или он берется извне.
    */
    private static function _pathIsLocal($imgPath)
    {
        return strpos($imgPath, '://') === false;
    }

    /*
        Получаем наш текущий домен
    */
    private static function _getDomain()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /*
        Получаем папку-корень проекта
    */
    private static function _getDocumentRoot(){
        return $_SERVER['DOCUMENT_ROOT'];
    }

    /*
        Разбивает имя файла на название и расширение
    */
    private static function _getExt($imgName) {
        $ext = substr($imgName, strrpos($imgName,'.') + 1);
        $name = substr($imgName, 0, strrpos($imgName,'.'));
        return ['ext' => $ext, 'name' => $name];
    }

    /*
        Установка модуля
    */
    public function _install() {
        if (! $this->dx_auth->is_admin()) {
            $this->core->error_404();
        }

        $this->db->where('name', 'image_resize')->update('components', [
            'autoload' => '1', 
            'enabled'  => '1', 
            'in_menu'  => '0',
        ]);
    }
    
}