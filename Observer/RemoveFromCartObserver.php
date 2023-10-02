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

class RemoveFromCartObserver extends ImpreseeRegisterStoreEventObserver
{

    public function __construct(LoggerInterface $logger, CodesHelper $codes,
     Header $httpHeader, RemoteAddress $remoteAddress,
     CustomerSession $customerSession)
    {
        parent::__construct($logger, $codes, $httpHeader, $remoteAddress, $customerSession, 'REMOVE_FROM_CART');
    }

    protected function buildEventUrl(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getQuoteItem()->getProduct();
        $sku = $product->getSku() ? $product->getSku() : '';
        $product_id = $product->getId() ? $product->getId() : '';
        $price = $product->getPrice() ? $product->getPrice() : '';
        $url_data ='sku='.urlencode($sku).'&pid='.urlencode($product_id).'&p='.urlencode($price);
        return $url_data;
    }

}