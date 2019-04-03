<?php
/**
 * Block used to generate DataFeed
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use ImpreseeAI\ImpreseeVisualSearch\Model\Products;
use ImpreseeAI\ImpreseeVisualSearch\Model\GenerateXml;

class ProductList extends Template
{
    /**
     *   To getCollection of products
     * @var ImpreseeAI\ImpreseeVisualSearch\Model\Products
     */
    public $products;
    /**
     *   To generate DataFeed xml
     * @var ImpreseeAI\ImpreseeVisualSearch\Model\GenerateXml
     */
    public $generate;
    /**
     *   Productlist constructor
     * @param Context $context
     * @param array $data
     * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products
     * @param ImpreseeAI\ImpreseeVisualSearch\Model\GenerateXml
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
