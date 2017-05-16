<?php


/**
 *
 * Base class for all Croppers
 *
 */
class Scale
{
    /**
     *
     * @var \Imagick
     */
    protected $originalImage = null;

    /**
     * baseDimension
     *
     * @var array
     * @access protected
     */

    public function setImagePath($imagePath)
    {
        if ($imagePath) {
            $this->originalImage = new \Imagick($imagePath);
        }
    }


    /**
     * Resize and crop the image so it dimensions matches $targetWidth and $targetHeight
     *
     * @param  int              $targetWidth
     * @param  int              $targetHeight
     * @return boolean|\Imagick
     */
    public function resizeAndScale($targetWidth, $targetHeight, $targetQuality)
    {
        $bestFit = ($targetWidth && $targetHeight);
        $this->originalImage->stripImage();
        $this->originalImage->setImageCompressionQuality($targetQuality);
        $this->originalImage->scaleImage($targetWidth, $targetHeight, $bestFit);
        
        return $this->originalImage;
    }
}