<?php
/**
 *  Functions to get impresee codes
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Setup;

class Requests extends AbstractHelper
{
    private $_setup;
    /**
     * General constructor.
     * @param Context $context
     */
    public function __construct(Context $context, Setup $setup)
    {
        parent::__construct($context);
        $this->_setup = $setup;
    }

    public function parseClientData() {
        $server_data = $_SERVER;
        return 'ip='.urlencode($server_data['REMOTE_ADDR']).'&ua='.urlencode($server_data['HTTP_USER_AGENT']).'&store='.urlencode($server_data['HTTP_HOST']);
    }
    public function getRegisterEventsUrl()
    {
        $baseUrl = $this->_setup->getIsDebug() ? 'https://dev2.impresee.com' : 'https://api.impresee.com';
        return $baseUrl.'/ImpreseeSearch/api/v3/search/register_magento/';
    }
    public function callRegisterEventUrl($app, $url_data) {

        $content = file($this->getRegisterEventsUrl().$app.'?'.$url_data);
    }
}
