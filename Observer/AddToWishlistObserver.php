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

class AddToWishlistObserver extends ImpreseeRegisterStoreEventObserver
{

    public function __construct(LoggerInterface $logger, CodesHelper $codes,
     Header $httpHeader, RemoteAddress $remoteAddress,
     CustomerSession $customerSession)
    {
        parent::__construct($logger, $codes, $httpHeader, $remoteAddress, $customerSession, 'ADD_TO_WISHLIST');
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