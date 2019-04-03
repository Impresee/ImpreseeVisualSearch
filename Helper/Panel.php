<?php
/**
 *  Functions to get custom js,css,and css classes
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Panel extends AbstractHelper
{
    /**
     * General constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }
    /**
     * Get custom javascript code added by user in impresee config
     * @param string with the storeid
     * @return string with the client custom js
     */
    public function getJavaCode($store)
    {
        return $this->scopeConfig->getValue("impresee/frontend/javacode", $store);
    }
   /**
    * Get custom css class names added by user in impresee config
    * @param string with the storeid
    * @return string with the css classes
    */
    public function getClasses($store)
    {
        return $this->scopeConfig->getValue("impresee/frontend/classes", $store);
    }
   /**
    * Get custom css styles to add in our module
    * @param string with the storeid
    * @return string with the css styles added by user on system config
    */
    public function getStyles($store)
    {
        return $this->scopeConfig->getValue("impresee/frontend/styles", $store);
    }
    /**
     * Get module sketch search feature status
     * @param string with the storeid
     * @return int (0|1)
     */
    public function getSketchEnableStatus($store)
    {
        if (null!=($this->scopeConfig->getValue("impresee/general/enable_sketch", $store))) {
            return $this->scopeConfig->getValue("impresee/general/enable_sketch", $store);
        } else {
            return 0;
        }
    }
    /**
     * Get module photo search feature status
     * @param string with the storeid
     * @return int (0|1)
     */
    public function getPhotoEnableStatus($store)
    {
        if (null!= ($this->scopeConfig->getValue("impresee/general/enable_photo", $store))) {
            return $this->scopeConfig->getValue("impresee/general/enable_photo", $store);
        } else {
            return 0;
        }
    }
    /**
     * Get "using module buttons" status
     * @param string with the storeid
     * @return int (0|1)
     */
    public function getEnableModuleIcons($store)
    {
        if (null!=($this->scopeConfig->getValue("impresee/icons/module_set_buttons", $store))) {
            return $this->scopeConfig->getValue("impresee/icons/module_set_buttons", $store);
        } else {
            return 0;
        }
    }
    /**
     * Get the path to image of sketch icon
     * @param string with the storeid
     * @return string with the URL of the image
     */
    public function getSketchIconRelativePath($store)
    {
        return $this->scopeConfig->getValue("impresee/icons/sketch_icon", $store);
    }
    /**
     * Get the path to image of photo icon
     * @param string with the storeid
     * @return string with the URL of the image
     */
    public function getPhotoIconRelativePath($store)
    {
        return $this->scopeConfig->getValue("impresee/icons/photo_icon", $store);
    }
    /**
     * Looks on db if URL of a specific kind of search exist.
     * @param string $type . Type of search
     * @param string with the storeid
     * @return int (0|1)
     */
    public function emptyUrl($type, $store)
    {
        $url = $this->scopeConfig->getValue("impresee/general/". $type ."_url", $store);
        if ((isset($url)) || (strlen($url) !=0)) {
            return 0;
        }
        return 1;
    }
    /**
     * Get name of photo icon (from Impresee Catalog)
     * @param string with the storeid
     * @return string with the css classes
     */
    public function getPhotoIconName($store)
    {
        return $this->scopeConfig->getValue("impresee/icons/photo_icon_name", $store);
    }
     /**
      * Get name of sketch icon (from Impresee Catalog)
      * @param string with the storeid
      * @return string with the css classes
      */
    public function getSketchIconName($store)
    {
        return $this->scopeConfig->getValue("impresee/icons/sketch_icon_name", $store);
    }
}
