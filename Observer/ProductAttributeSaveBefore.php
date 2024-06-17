<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;

use ImpreseeAI\ImpreseeVisualSearch\Observer\ImpreseeCatalogObserver;

class ProductAttributeSaveBefore extends ImpreseeCatalogObserver
{
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    try {
    $uuid = $this->getImpreseeCatalogUuid();
    $this->doRequest("attributes/{$uuid}");

    $productIds = $observer->getEvent()->getProductIds();
    $this->doMultipleRequest("productsByIds", $productIds);

    } catch (\Exception $e) {
        $this->_logger->debug($e->getMessage());
    }
  }
}
