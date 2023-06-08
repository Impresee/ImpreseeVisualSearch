<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Requests as RequestsHelper;

abstract class ImpreseeRegisterStoreEventObserver implements ObserverInterface
{
    protected $logger;
    /**
   * load codes of our app
   * @var ImpreseeAI\ImpreseeVisualSearch\Helper\Codes
   */
    protected $_codesHelper;
    protected $_requestsHelper;
    protected $_event;

    public function __construct(LoggerInterface $logger, CodesHelper $codes, RequestsHelper $requestsHelper, $event)
    {
        $this->logger = $logger;
        $this->_codesHelper = $codes;
        $this->_event = $event;
        $this->_requestsHelper = $requestsHelper;
    }

    abstract protected function buildEventUrl(\Magento\Framework\Event\Observer $observer);

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $photo_app = $this->_codesHelper->getImpreseeUuid(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (!$photo_app) return;
            $url_data = $this->buildEventUrl($observer);
            $parsed_client = $this->_requestsHelper->parseClientData();
            $url_data .= '&evt='.urlencode($this->_event).'&a='.urlencode($this->_codesHelper->getRegisterEventsAction()).'&'.$parsed_client;
            $this->logger->debug($url_data);
            $this->_requestsHelper->callRegisterEventUrl($photo_app, $url_data);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}