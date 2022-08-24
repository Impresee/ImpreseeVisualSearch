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
    public function getCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        
        return $collection;
    }
}
