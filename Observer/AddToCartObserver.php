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

class AddToCartObserver extends ImpreseeRegisterStoreEventObserver
{

    protected $request;

    public function __construct(LoggerInterface $logger, CodesHelper $codes,
     Header $httpHeader, RemoteAddress $remoteAddress,
     \Magento\Framework\App\RequestInterface $request,
     CustomerSession $customerSession)
    {
        $this->request = $request;
        parent::__construct($logger, $codes, $httpHeader, $remoteAddress, $customerSession, 'ADD_TO_CART');
    }

    protected function buildEventUrl(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $sku = $product->getSku() ? $product->getSku() : '';
        $product_id = $product->getId() ? $product->getId() : '';
        $price = $product->getPrice() ? $product->getPrice() : '';
        $qty = $product->getQty() ? $product->getQty() : '';
        $from_impresee_text = $this->request->getParam('source_impresee', '');
        $from_impresee_visual = $this->request->getParam('seecd', ''); 
        $url_data = 'fi='.urlencode($from_impresee_text).'&fiv='.urlencode($from_impresee_visual).'&qty='.urlencode($qty).'&sku='.urlencode($sku).'&pid='.urlencode($product_id).'&p='.urlencode($price);
        return $url_data;
    }

}