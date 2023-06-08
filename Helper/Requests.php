<?php
/**
 *  Functions to get impresee codes
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Requests extends AbstractHelper
{
    /**
     * General constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function parseClientData() {
        $server_data = $_SERVER;
        return 'ip='.urlencode($server_data['REMOTE_ADDR']).'&ua='.urlencode($server_data['HTTP_USER_AGENT']).'&store='.urlencode($server_data['HTTP_HOST']);
    }
    public function getRegisterEventsUrl()
    {
        return 'https://api.impresee.com/ImpreseeSearch/api/v3/search/register_magento/';
    }
    public function callRegisterEventUrl($app, $url_data) {

        $content = file($this->getRegisterEventsUrl().$app.'?'.$url_data);
    }
}
