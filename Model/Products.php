<?php
/**
 * Creates a product collection object
 */
namespace Impresee\ImpreseeVisualSearch\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Products
{
  /**
   * To create a product collection
   * @var Magento\Catalog\Model\ResourceModel\Product\Collection
   */
    protected $productCollectionFactory;
  /**
   * Constructor.
   * @param ProductCollectionFactory          $productCollectionFactory
   * @param ProductAttributeCollectionFactory $productAttributeCollectionFactory
   */
    public function __construct(ProductCollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }
  /**
   * Creates a collection
   * @return collection of products
   */
    public function getCollection()
    {
        $collection = $this->productCollectionFactory->create();
        return $collection;
    }
}
