<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use Magento\Store\Model\StoreManagerInterface;

class ProductStockChangeObserver implements ObserverInterface
{
    protected $logger;
    /**
   * load codes of our app
   * @var ImpreseeAI\ImpreseeVisualSearch\Helper\Codes
   */
    protected $_codesHelper;
    protected $_stockItemRepository;
    protected $_storeManager;

    public function __construct(LoggerInterface $logger, CodesHelper $codes,
         StockItemRepository $stockItemRepository,
         StoreManagerInterface $storeManager)
    {
        $this->logger = $logger;
        $this->_codesHelper = $codes;
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
            $store = $this->storeManager->getStore();
            $_product = $observer->getProduct();
            $photo_app = $this->_codesHelper->getPhotoUrl($store);
            // $photo_app es NULL
            if (!$photo_app) return;
            $action = 'CHANGE_PRODUCT';
            $event_type = 'magento_2_0';
            $id = $_product->getId();
            $_productStock = $this->getStockItem($id);
            $this->callUpdateProductUrl($photo_app, $_product, $_productStock);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    

    private function callUpdateProductUrl($app, $product, $productStock) {
        // TODO: Register change
       
    }

}