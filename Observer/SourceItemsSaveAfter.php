<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;

use ImpreseeAI\ImpreseeVisualSearch\Observer\ImpreseeCatalogObserver;

class SourceItemsSaveAfter extends ImpreseeCatalogObserver
{
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    try {
        $sourceItems = $observer->getEvent()->getSourceItems();
        $skus = [];

        foreach ($sourceItems as $sourceItem) {
            $skus[] = $sourceItem->getSku();
        }
        $this->doMultipleRequest("productsBySkus", $skus);

    } catch (\Exception $e) {
        $this->_logger->debug($e->getMessage());
    }
  }
}
