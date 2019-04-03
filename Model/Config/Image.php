<?php
/**
 * Load icon images on admin side
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Model\Config;

use Magento\Config\Model\Config\Backend\File;

class Image extends File
{
    /**
     * Getter for allowed extensions of uploaded files
     * @return string[] with extensions
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
