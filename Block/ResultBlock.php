<?php
/**
 *   Uses (extends) ListProduct to display the list of products
 *   provided by Impresee
 */
namespace Impresee\ImpreseeVisualSearch\Block;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Block\Product\Context;

/**
 *  Extends from core ListProduct block
 */
class ResultBlock extends ListProduct
{
    /**
     *  Impresee app uuid used in the search
     * @var string
     */
    public $application_uuid;
    /**
     *   Overrided
     * @return collection
     */
    protected function _getLoadedProductCollection()
    {
        return $this->_productCollection;
    }
    /**
     *  Set a productCollection from controller
     * @param collection
     */
    public function setProductCollection($collection)
    {
         $this->_productCollection = $collection;
    }
    /**
     *  Set application uuid from controller
     * @param code
     */
    public function setAppUuid($code)
    {
        $this->application_uuid = $code;
    }
}
