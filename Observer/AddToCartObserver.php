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

class AddToCartObserver extends ImpreseeRegisterStoreEventObserver
{

    public function __construct(LoggerInterface $logger, CodesHelper $codes, RequestsHelper $requests)
    {
        parent::__construct($logger, $codes, $requests, 'ADD_TO_CART');
    }

    protected function buildEventUrl(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $sku = $product->getSku() ? $product->getSku() : '';
        $product_id = $product->getId() ? $product->getId() : '';
        $price = $product->getPrice() ? $product->getPrice() : '';
        $qty = $product->getQty() ? $product->getQty() : '';
        $from_impresee_text = isset($_GET['source_impresee']) ? $_GET['source_impresee'] : '';
        $from_impresee_visual = isset($_GET['seecd']) ? $_GET['seecd'] : '';
        $url_data = 'fi='.urlencode($from_impresee_text).'&fiv='.urlencode($from_impresee_visual).'&qty='.urlencode($qty).'&sku='.urlencode($sku).'&pid='.urlencode($product_id).'&p='.urlencode($price);
        return $url_data;
    }

}