<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class InventoryStockSaveAfter extends ImpreseeCatalogObserver
{
    public function execute(Observer $observer)
    {
        try {
            $uuid = $this->getImpreseeCatalogUuid();

            // Retrieve the stock item from the event observer
            $stockItem = $observer->getEvent()->getItem();
            // Get the product ID
            $productId = $stockItem->getProductId();

            // Fetch the product SKU using the getProductSkuById method
            $sku = $this->getProductSkuById($productId);

            if ($sku !== null) {
                // Perform the request using the product SKU
                $this->doRequest("stock/{$uuid}/{$sku}");
            } else {
                $this->_logger->error('Failed to retrieve SKU for Product ID ' . $productId);
            }
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
    }
}

