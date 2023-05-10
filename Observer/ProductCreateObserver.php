<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

class ProductCreateObserver implements ObserverInterface
{
    protected $logger;
    /**
   * load codes of our app
   * @var ImpreseeAI\ImpreseeVisualSearch\Helper\Codes
   */
    protected $_codesHelper;

    public function __construct(LoggerInterface $logger, CodesHelper $codes)
    {
        $this->logger = $logger;
        $this->_codesHelper = $codes;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $_product = $observer->getProduct();
            $photo_app = $this->_codesHelper->getPhotoUrl(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (!$photo_app) return;
            $action = 'CONVERSION';
            $event_type = 'magento_2_0';
            $sku = $_product->getSku();
            
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    

    private function callConversionUrl($app, $url_data) {

        $register_conversion_endpoint = 'https://api.impresee.com/ImpreseeSearch/api/v3/search/register_magento/';
        $content = file($register_conversion_endpoint.$app.'?'.$url_data);
    }

    private function parseItems(array $items)
    {
        $attributes = array();
        $product_ids = array();
        $product_names = array();
        $quantities = array();
        $prices = array();
        $skus = array();
        $types = array();
        foreach ($items as $item) {
            array_push($product_ids, $item->getProductId());
            array_push($product_names, $item->getProductName());
            array_push($quantities, $item->getQtyOrdered());
            array_push($prices, $item->getPriceInclTax());
            array_push($skus, $item->getSku());
            array_push($types,$item->getProductType());
        }
        return 'prodids='.urlencode(join('|', $product_ids)).'&types='.urlencode(join('|', $types)).'&qtys='.urlencode(join('|', $quantities)).'&ps='.urlencode(join('|', $prices)).'&skus='.urlencode(join('|', $skus));
    }
}