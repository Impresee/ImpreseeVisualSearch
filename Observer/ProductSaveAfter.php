<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductSaveAfter extends ImpreseeCatalogObserver
{
    public function execute(Observer $observer)
    {
        try {
            $uuid = $this->getImpreseeCatalogUuid();
            $product = $observer->getProduct();
            if ($product) {
                $sku = $product->getSku();
                $this->doRequest("productNoStock/{$uuid}/{$sku}");
            }
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
    }
}

