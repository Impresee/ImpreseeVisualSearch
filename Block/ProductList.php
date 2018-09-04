<?php
/**
 * Block used to generate DataFeed
 */
namespace Impresee\ImpreseeVisualSearch\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Impresee\ImpreseeVisualSearch\Model\Products;
use Impresee\ImpreseeVisualSearch\Model\GenerateXml;

class ProductList extends Template
{
    /**
     *   To getCollection of products
     * @var Impresee\ImpreseeVisualSearch\Model\Products
     */
    public $products;
    /**
     *   To generate DataFeed xml
     * @var Impresee\ImpreseeVisualSearch\Model\GenerateXml
     */
    public $generate;
    /**
     *   Productlist constructor
     * @param Context $context
     * @param array $data
     * @param Impresee\ImpreseeVisualSearch\Model\Products
     * @param Impresee\ImpreseeVisualSearch\Model\GenerateXml
     */
    public function __construct(
        Context $context,
        array $data,
        Products $products,
        GenerateXml $generate
    ) {
        parent::__construct($context, $data);
        $this->products = $products;
        $this->generate = $generate;
    }
}
