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

class ClearCompareListObserver extends ImpreseeRegisterStoreEventObserver
{

    public function __construct(LoggerInterface $logger, CodesHelper $codes, RequestsHelper $requests)
    {
        parent::__construct($logger, $codes, $requests, 'CLEAR_COMPARE_LIST');
    }

    protected function buildEventUrl(\Magento\Framework\Event\Observer $observer)
    {
        return 'des=clear_compare';
    }

}