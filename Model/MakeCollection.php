<?php
/**
 *  Makes a product collection with the products of a Impresee serarch results
 */
namespace Impresee\ImpreseeVisualSearch\Model;

use Impresee\ImpreseeVisualSearch\Model\Products as ProductCollection;

class MakeCollection
{
  /**
   * Collection of products
   * @var Impresee\ImpreseeVisualSearch\Model\Products
   */
    protected $_productCollection;
  /**
   * Constructor
   * @param Impresee\ImpreseeVisualSearch\Model\Products
   */
    public function __construct(ProductCollection $productCollection)
    {
          $this->_productCollection = $productCollection;
    }
  /**
   *   Make a product collection using a list of skus sorted by their index
   * @param string[] with skus
   * @return collection of products
   */
    public function makeCollectionBySkuSorted($skus)
    {
        $collection = $this->_productCollection->getCollection()
        ->addAttributeToFilter('sku', ['in'=> $skus])
        ->addAttributeToSelect('*');
        $collection->getSelect()->order("find_in_set(sku,'".implode(',', $skus)."')");
        return $collection;
    }
  /**
   *   Make a product collection using a list of skus sorted by their index
   * @param string[] with skus
   * @return collection of products
   */
    public function makeCollectionBySku($skus)
    {
        $collection = $this->_productCollection->getCollection()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('sku', ['in'=> $skus]);
        return $collection;
    }
}
