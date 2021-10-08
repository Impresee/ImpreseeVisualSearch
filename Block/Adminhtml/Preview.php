<?php
/**
 *   Display a preview of the frontend on an iframe
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

class Preview extends Field
{
    /**
     *  To fetch client code from db
     * @var CodesHelper
     */
    public $codesHelper;
    /**
     * Id of current store
     * @var int
     */
    protected $_storeId;
    /**
     * @param Context
     */
    public function __construct(
        Context $context,
        CodesHelper $CodesHelper,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
        $this->codesHelper = $CodesHelper;
        $this->_storeId = (int) $request->getParam('store', 0);
    }
    /**
     *   Displays an iframe with frontend home in system config
     *   (as a preview for the changes on impresee buttons)
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $url   = $this->_storeManager->getStore($this->_storeId)->getBaseUrl() . "impresee/preview?client_code=" . $this->codesHelper->getClientCode().'&page=1';
        $html = '   <td></td>';
        $html .= '     <span>A preview of your frontend store can be visited <a href="'.$url.'">here</a> (don\'t forget to see it on mobile)</span>';
        return $html;
    }
}
