<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use ImpreseeAI\ImpreseeVisualSearch\Observer\ImpreseeRegisterStoreEventObserver;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

class CustomerCreateAccountObserver extends ImpreseeRegisterStoreEventObserver
{

    public function __construct(LoggerInterface $logger, CodesHelper $codes)
    {
        parent::__construct($logger, $codes, 'CREATE_ACCOUNT');
    }

    protected function buildEventUrl(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $firstname = $customer->getFirstname() ? $customer->getFirstname() : '';
        $lastname = $customer->getLastname() ? $customer->getLastname() : '';
        $email = $customer->getEmail() ? $customer->getEmail() : '';
        $url_data = 'cn='.urlencode($firstname.' '.$lastname).'&cem='.urlencode($email);
        return $url_data;
    }

}