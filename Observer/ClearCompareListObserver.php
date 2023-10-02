<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use ImpreseeAI\ImpreseeVisualSearch\Observer\ImpreseeRegisterStoreEventObserver;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use \Magento\Framework\HTTP\Header;
use Magento\Customer\Model\Session as CustomerSession;

class ClearCompareListObserver extends ImpreseeRegisterStoreEventObserver
{

    public function __construct(LoggerInterface $logger, CodesHelper $codes,
     Header $httpHeader, RemoteAddress $remoteAddress,
     CustomerSession $customerSession)
    {
        parent::__construct($logger, $codes, $httpHeader, $remoteAddress, $customerSession, 'CLEAR_COMPARE_LIST');
    }

    protected function buildEventUrl(\Magento\Framework\Event\Observer $observer)
    {
        return 'des=clear_compare';
    }

}