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
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $action = 'CONVERSION';
            $event_type = 'magento_2_0';
            $order = $observer->getEvent()->getOrder();
            $server_data = $_SERVER;
            $order_id = $order->getIncrementId();
            $parsed_items = $this->parseItems($order->getItems());
            $parsed_customer = $this->parseCustomer($order);
            $parsed_client = $this->parseClient($server_data);
            $url_data = 'a='.urlencode($action).'&evt='.urlencode($event_type).'&ref='.urlencode($order_id).'&'.$parsed_items.'&'.$parsed_customer.'&'.$parsed_client.'&tdis='.urlencode($order->total_discounts_tax_incl).'&tord='.urlencode($order->total_paid_tax_incl);
            $url = $this->_codesHelper->getPhotoUrl(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $impresee_app = $this->_codesHelper->getCode($url);
            $this->callConversionUrl($impresee_app, $url_data);
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
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
            array_push($product_ids, $item->id);
            array_push($product_names, $item->product_name);
            array_push($attributes_id, $product['product_attribute_id']);
            array_push($quantities, $item->quantity_invoiced);
            array_push($prices, $item->product_sale_price);
            array_push($skus, $item->product_sku);
            array_push($types,$item->product_type);
            $product_attributes = array();
            foreach ($item->selected_options as $option) {
                array_push($product_attributes, $option->label.'-'.$option->value);
            }
            $str_attributes = join('*', $product_attributes);
            array_push($attributes, $str_attributes);
        }
        return 'prodids='.urlencode(join('|', $product_ids)).'types='.urlencode(join('|', $types)).'&attrids='.urlencode(join('|', $attributes)).'&qtys='.urlencode(join('|', $quantities)).'&ps='.urlencode(join('|', $prices)).'&skus='.urlencode(join('|', $skus));
    }

    private function parseCustomer(\Magento\Sales\Model\Order $order)
    {
        $id = $order->getCustomerId();
        $firstname = $order->getCustomerFirstname() != null ? $order->getCustomerFirstname() : '';
        $lastaname = $order->getCustomerLastname() != null ? $order->getCustomerLastname() : '';
        $email = $order->getCustomerEmail();
        return 'cid='.urlencode($id).'&cfn='.urlencode($firstname).'&cln='.urlencode($lastname).'&cem='.urlencode($email);
    }

    private function parseClient($server_data) {
        return 'ip='.urlencode($server_data['REMOTE_ADDR']).'&ua='.urlencode($server_data['HTTP_USER_AGENT']).'&store='.urlencode($server_data['HTTP_HOST']);
    }
}