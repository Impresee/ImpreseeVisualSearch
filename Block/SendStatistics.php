<?php
/**
 *  Convert the results of a search to js object
 *  to use it in view (stats.php)
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

/**
 *  Extends from core ListProduct block
 */
class SendStatistics extends Template
{
    /**
     *   To access data from singleton registry loaded in controller
     * @var Magento\Framework\Registry
     */
    public $registry;
    /**
     * @param Context
     * @param array
     * @param Magento\Framework\Registry
     */
    public function __construct(
        Context $context,
        array $data,
        Registry $registry
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }
    /**
     *  Creates a string representing a js object, which contains ranks and urls from
     *  the products on the response (useful for maintaining a sorted list of results).
     * @return string (javascript object like)
     */
    public function dataToJavascript()
    {
        $data   = $this->registry->registry('rankData');
        $result = '';
        if (is_array($data)) {
            $result = '{';
            foreach ($data as $pair) :
                {
                $result .= '"'.$pair[0].'": '. strval($pair[1]).',';
                }
            endforeach;
              $result .= '}';
        } else {
            $result = "{}";
        }
        return $result;
    }
}
