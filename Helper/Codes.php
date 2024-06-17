<?php
/**
 *  Functions to get impresee codes
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Codes extends AbstractHelper
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
     *   Get client_code generated on module installation, saved on db
     * @return string with client_code
     */
    public function getClientCode()
    {
        return $this->scopeConfig->getValue("impresee/general/client_code");
    }
    /**
     *   Get sketch app code saved on config. Provided by impresee
     *   (Needed to make a sketch search)
     * @param string with the storeid
     * @return string with the sketch search code for a store
     */
    public function getSketchUrl($store)
    {
        return $this->scopeConfig->getValue("impresee/general/sketch_url", $store);
    }

    /**
     *   Get photo app code saved on config. Provided by impresee
     *   (Needed to make a photo search)
     * @param string with the storeid
     * @return string with the photo search code for a store
     */
    public function getImpreseeUuid($store)
    {
        return $this->scopeConfig->getValue("impresee/general/impresee_app_uuid", $store);
    }

    /**
     *   Get catalog uuid saved on config. Provided by impresee
     * @param string with the storeid
     * @return string with the Impresee catalog uuid
     */
    public function getImpreseeCatalogUuid($store = null)
    {
    return $this->scopeConfig->getValue("impresee/general/impresee_catalog_uuid", $store);
    }

    /**
     * Fetch the Impresee app code from a Impresee service url
     * @param string with the URL
     * @return string with the app code
     */
    public function getCode($url)
    {
        $code = [];
        preg_match("#^.*\/([a-zA-Z0-9\_-]*)$#", $url, $code);
        if (isset($code[1])) {
            return $code[1];
        }
        return "error";
    }
    public function getConsumerKey($store)
    {
        return $this->scopeConfig->getValue("impresee/api_access/consumer_key", $store);
    }
    public function getConsumerSecret($store)
    {
        return $this->scopeConfig->getValue("impresee/api_access/consumer_secret", $store);
    }
    public function getAccessToken($store)
    {
        return $this->scopeConfig->getValue("impresee/api_access/access_token", $store);
    }
    public function getAccessTokenSecret($store)
    {
        return $this->scopeConfig->getValue("impresee/api_access/access_token_secret", $store);
    }
    public function getRegisterEventsPlatformEvent()
    {
        return 'magento_2_0';
    }
}
