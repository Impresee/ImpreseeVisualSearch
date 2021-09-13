<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

class ConversionObserver implements ObserverInterface
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
            $action = 'CONVERSION';
            $event_type = 'magento_2_0';
            $order = $observer->getEvent()->getOrder();
            $server_data = $_SERVER;
            $order_id = $order->getIncrementId();
            $parsed_items = $this->parseItems($order->getAllItems());
            $parsed_customer = $this->parseCustomer($order);
            $parsed_client = $this->parseClient($server_data);
            $currency = $order->getOrderCurrencyCode() != null ? $order->getOrderCurrencyCode() : '';
            $discount = $order->getDiscountAmount() != null ? $order->getDiscountAmount()() : 0;
            $url_data = 'a='.urlencode($action).'&evt='.urlencode($event_type).'&ref='.urlencode($order_id).'&'.$parsed_items.'&'.$parsed_customer.'&'.$parsed_client.'&tdis='.urlencode($discount).'&tord='.urlencode($order->getTotalDue()).'&curr='.urlencode($currency);
            $photo_app = $this->_codesHelper->getPhotoUrl(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->callConversionUrl($photo_app, $url_data);
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
        return 'prodids='.urlencode(join('|', $product_ids)).'types='.urlencode(join('|', $types)).'&qtys='.urlencode(join('|', $quantities)).'&ps='.urlencode(join('|', $prices)).'&skus='.urlencode(join('|', $skus));
    }

    private function parseCustomer(\Magento\Sales\Model\Order $order)
    {
        $id = $order->getCustomerId();
        $firstname = $order->getCustomerFirstname() != null ? $order->getCustomerFirstname() : '';
        $lastname = $order->getCustomerLastname() != null ? $order->getCustomerLastname() : '';
        $email = $order->getCustomerEmail();
        return 'cid='.urlencode($id).'&cfn='.urlencode($firstname).'&cln='.urlencode($lastname).'&cem='.urlencode($email);
    }

    private function parseClient($server_data) {
        return 'ip='.urlencode($server_data['REMOTE_ADDR']).'&ua='.urlencode($server_data['HTTP_USER_AGENT']).'&store='.urlencode($server_data['HTTP_HOST']);
    }
}