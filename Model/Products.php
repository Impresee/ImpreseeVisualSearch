<?php
/**
 * Creates a product collection object
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Model;

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
    public function __construct(ProductCollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->productStatus = $productStatus;
    }
  /**
   * Creates a collection
   * @return collection of products
   */
    public function getCollection($includeOutOfStock)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        if (!$includeOutOfStock){
            $collection->joinField(
                'stock_status', 'cataloginventory_stock_status', 'stock_status', 'product_id=entity_id', '{{table}}.stock_id=1', 'left'
            );
            $collection->addFieldToFilter('stock_status', array('eq' => \Magento\CatalogInventory\Model\Stock\Status::STATUS_IN_STOCK));
        }
        
        
        
        return $collection;
    }
}
