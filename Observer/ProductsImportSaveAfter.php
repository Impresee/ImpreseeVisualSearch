<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;

use ImpreseeAI\ImpreseeVisualSearch\Observer\ImpreseeCatalogObserver;

class ProductsImportSaveAfter extends ImpreseeCatalogObserver
{
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    $bunch = $observer->getBunch();
    $skus = [];
    foreach ($bunch as $product) {
        $json_product = json_encode($product);
        $json_product = json_decode($json_product, true);
        $skus[] = $json_product['sku'];
    }
    $this->doMultipleRequest("productsBySkus", $skus);
  }
}
