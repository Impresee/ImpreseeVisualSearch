<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use ImpreseeAI\ImpreseeVisualSearch\Observer\ImpreseeRegisterStoreEventObserver;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Requests as RequestsHelper;

class CustomerLoginObserver extends ImpreseeRegisterStoreEventObserver
{

    public function __construct(LoggerInterface $logger, CodesHelper $codes, RequestsHelper $requests)
    {
        parent::__construct($logger, $codes, $requests, 'LOGIN');
    }

    protected function buildEventUrl(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $customer_id = $customer->getId() ? $customer->getId() : ''; 
        $name = $customer->getName() ? $customer->getName() : '';
        $email = $customer->getEmail() ? $customer->getEmail() : '';
        $url_data = 'cn='.urlencode($name).'&cem='.urlencode($email).'&cid='.urlencode($customer_id);
        return $url_data;
    }

}