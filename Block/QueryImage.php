<?php
/**
 *   Used to display query image used on search
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

class QueryImage extends Template
{
    /**
     *   To save image's url used for search
     * @var string
     */
    public $queryImageUrl;
    /**
     * address of Impresee Logo
     * @var string
     */
    public $impreseeLogo;
    /**
     *   To access data from singleton registry loaded in controller
     * @var Registry
     */
    public $registry;
    /**
     *   QueryImage constructor, fetch to $this->queryImageUrl the string with the url
     *   of the image used to search with Impresee
     * @param Context $context
     * @param array $data
     * @param Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        array $data,
        Registry $registry
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $data = $this->registry->registry('searchResults');
        if ($data['status'] == 0) {
            $serverUrl = $data['server_url'];
            $queryUrl  = $data['query_url'];
            $this->queryImageUrl = $serverUrl.$queryUrl;
        }
        $this->impreseeLogo = $this->getViewFileUrl('ImpreseeAI_ImpreseeVisualSearch::images/ImpreseeLogo.svg');
    }
}
