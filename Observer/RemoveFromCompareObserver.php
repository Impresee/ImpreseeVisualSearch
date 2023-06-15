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

class RemoveFromCompareObserver extends ImpreseeRegisterStoreEventObserver
{

    public function __construct(LoggerInterface $logger, CodesHelper $codes, RequestsHelper $requests)
    {
        parent::__construct($logger, $codes, $requests, 'REMOVE_FROM_COMPARE');
    }

    protected function buildEventUrl(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();;
        $sku = $product->getSku() ? $product->getSku() : '';
        $product_id = $product->getId() ? $product->getId() : '';
        $url_data = 'sku='.urlencode($sku).'&pid='.urlencode($product_id);
        return $url_data;
    }

}