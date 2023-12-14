<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\HTTP\Header;
use Magento\Customer\Model\Session as CustomerSession;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

abstract class ImpreseeRegisterStoreEventObserver implements ObserverInterface
{
    const EVENT_TYPE = 'magento_2_0';
    protected $logger;
    /**
   * load codes of our app
   * @var ImpreseeAI\ImpreseeVisualSearch\Helper\Codes
   */
    protected $_codesHelper;
    protected $_action;
    private $_remoteAddress;
    private $_httpHeader;
    private $_customerSession;

    public function __construct(LoggerInterface $logger, CodesHelper $codes,
        Header $httpHeader, RemoteAddress $remoteAddress,
        CustomerSession $customerSession, $action)
    {
        $this->logger = $logger;
        $this->_codesHelper = $codes;
        $this->_action = $action;
        $this->_remoteAddress = $remoteAddress;
        $this->_httpHeader = $httpHeader;
        $this->_customerSession = $customerSession;
    }

    abstract protected function buildEventUrl(\Magento\Framework\Event\Observer $observer);

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $photo_app = $this->_codesHelper->getImpreseeUuid(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (!$photo_app) return;
            $server_data = $_SERVER;
            $url_data = $this->buildEventUrl($observer);
            $parsed_client = $this->parseClient($server_data);
            $url_data .= '&a='.urlencode($this->_action).'&evt='.urlencode(static::EVENT_TYPE).'&'.$parsed_client;
            // checks customer is logged in 
            if ($this->_customerSession->isLoggedIn()) {
                $customer_id = $this->_customerSession->getCustomerId();
                $url_data .= '&cid='.urlencode($customer_id);
            }
            $this->callRegisterEventUrl($photo_app, $url_data);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    private function callRegisterEventUrl($app, $url_data) {

        $register_conversion_endpoint = 'https://api.impresee.com/ImpreseeSearch/api/v3/search/register_magento/';
        $content = file($register_conversion_endpoint.$app.'?'.$url_data);
    }

    private function parseClient($server_data) {
        return 'ip='.urlencode($this->_remoteAddress->getRemoteAddress()).'&ua='.urlencode($this->_httpHeader->getHttpUserAgent()).'&store='.urlencode($server_data['HTTP_HOST']);
    }
}