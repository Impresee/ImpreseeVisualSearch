<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use ImpreseeAI\ImpreseeVisualSearch\ImpreseeObserver;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use Magento\Store\Model\StoreManagerInterface;

class ProductStockChangeObserver extends ImpreseeObserver
{
    const API_REQUEST_URI = '';
    protected $_stockItemRepository;
    protected $_storeManager;

    public function __construct(
        LoggerInterface $logger,
        CodesHelper $codes,
        StockItemRepository $stockItemRepository,
        StoreManagerInterface $storeManager,
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory)
    {
        parent::__construct($logger, $codes, $clientFactory, $responseFactory);
        $this->_stockItemRepository = $stockItemRepository;
        $this->_storeManager = $storeManager;
    }

    private function getStockItem($productId)
    {
        return $this->_stockItemRepository->get($productId);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $event = $observer->getEvent();
            $event_name = $event->getName();
            $store = $this->storeManager->getStore();
            $product = $observer->getProduct();
            $impresee_uuid = $this->_codesHelper->getImpreseeUuid($store);
            // $photo_app es NULL
            if (!$impresee_uuid) return;
            $action = 'CHANGE_PRODUCT';
            $event_type = 'magento_2_0';
            $id = $product->getId();
            $productStock = $this->getStockItem($id);
            $this->callUpdateProductUrl($photo_app, $product, $productStock);
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
    }

    private function callUpdateProductUrl($app, $product, $productStock) {
        $response = $this->doJsonRequest(static::API_REQUEST_ENDPOINT . $app, array(
            'product_id' => $product->getId(),
            'product_sku' => $product->getSku(),
            'in_stock' => $productStock->getIsInStock()
        ));
        $status = $response->getStatusCode(); // 200 status code
        $responseBody = $response->getBody();
        $responseContent = $responseBody->getContents(); // here you will have the API response in JSON format
       
    }

}