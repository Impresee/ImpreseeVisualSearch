<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\HTTP\Header;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

class ConversionObserver implements ObserverInterface
{
    protected $logger;
    /**
   * load codes of our app
   * @var ImpreseeAI\ImpreseeVisualSearch\Helper\Codes
   */
    protected $_codesHelper;
    private $_remoteAddress;
    private $_httpHeader;

    public function __construct(LoggerInterface $logger, CodesHelper $codes,
        Header $httpHeader, RemoteAddress $remoteAddress)
    {
        $this->logger = $logger;
        $this->_codesHelper = $codes;
        $this->_remoteAddress = $remoteAddress;
        $this->_httpHeader = $httpHeader;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $photo_app = $this->_codesHelper->getImpreseeUuid(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (!$photo_app) return;
            $action = 'CONVERSION';
            $event_type = 'magento_2_0';
            $order = $observer->getEvent()->getOrder();
            $server_data = $_SERVER;
            $order_id = $order->getIncrementId();
            $real_order_id = $order->getRealOrderId();
            $status_label = $order->getStatusLabel();
            $parsed_items = $this->parseItems($order->getAllItems());
            $payment_method = $this->parsePaymentMethod($order);
            $parsed_customer = $this->parseCustomer($order);
            $parsed_client = $this->parseClient($server_data);
            $currency = $order->getOrderCurrencyCode() != null ? $order->getOrderCurrencyCode() : '';
            $discount = $order->getDiscountAmount() != null ? $order->getDiscountAmount() : 0;
            $url_data = 'a='.urlencode($action).'&evt='.urlencode($event_type).'&'.$payment_method.'&ref='.urlencode($order_id).'&roi='.urlencode($real_order_id).'&sta='.urlencode($status_label).'&'.$parsed_items.'&'.$parsed_customer.'&'.$parsed_client.'&tdis='.urlencode($discount).'&tord='.urlencode($order->getTotalDue()).'&curr='.urlencode($currency);
            $this->callConversionUrl($photo_app, $url_data);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    

    private function callConversionUrl($app, $url_data) {

        $register_conversion_endpoint = 'https://api.impresee.com/ImpreseeSearch/api/v3/search/register_magento/';
        $this->logger->debug($register_conversion_endpoint.$app.'?'.$url_data);
        $content = file($register_conversion_endpoint.$app.'?'.$url_data);
    }

    private function parsePaymentMethod($order) {
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        return 'pmt='.urlencode($method->getTitle()).'&pmc='.urlencode($method->getCode());
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

    private function parseCustomer(\Magento\Sales\Model\Order $order)
    {
        $id = $order->getCustomerId();
        $firstname = $order->getCustomerFirstname() != null ? $order->getCustomerFirstname() : '';
        $lastname = $order->getCustomerLastname() != null ? $order->getCustomerLastname() : '';
        $email = $order->getCustomerEmail();
        return 'cid='.urlencode($id).'&cfn='.urlencode($firstname).'&cln='.urlencode($lastname).'&cem='.urlencode($email);
    }

    private function parseClient($server_data) {
        return 'ip='.urlencode($this->_remoteAddress->getRemoteAddress()).'&ua='.urlencode($this->_httpHeader->getHttpUserAgent()).'&store='.urlencode($server_data['HTTP_HOST']);
    }
}