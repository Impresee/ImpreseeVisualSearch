<?php
/**
 *   Display client code in system configuration page
 */
namespace Impresee\ImpreseeVisualSearch\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Impresee\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

class JavascriptComment extends Field
{
    /**
     *  To fetch client code from db
     * @var CodesHelper
     */
    public $codesHelper;
    /**
     * To fetch base url of the client server
     * @var StoreManagerInterface
     */
    protected $_storeManagerInterface;
    /**
     * Id of current store
     * @var int
     */
    protected $_storeId;
    /**
     * @param Context
     * @param Impresee\ImpreseeVisualSearch\Helper\Codes
     */
    public function __construct(
        Context $context,
        CodesHelper $CodesHelper,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_storeId = (int) $request->getParam('store', 0);
        parent::__construct($context);
        $this->codesHelper = $CodesHelper;
        $this->_storeManagerInterface = $this->_storeManager;
    }
    /**
     * Display comment on frontend group in module system config
     * @param AbstractElement
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $url   = $this->_storeManagerInterface->getStore($this->_storeId)->getBaseUrl() . "impresee/preview?client_code=" . $this->codesHelper->getClientCode();
        $html  = '<div class="comment">';
        $html .=  '<ul>';
        $html .=    '<li>Add javascript/Jquery and/or CSS to customize the behavior and look of the visual search buttons. Make sure to set their classes to <strong>impresee-photo-button</strong> and <strong>impresee-sketch-button</strong>.</li>';
        $html .=    '<li>If you need to change the behaviour of Impresee\'s buttons (e.g. move them somewhere else in the site) code it here!</li>';
        $html .=    '<li>A preview of your frontend store can be seen <a href="'.$url.'">here</a> (don\'t forget to take a look at the preview on a mobile device).</li>';
        $html .=    '<li>Please refer to the user guide to see an example.</li>';
        $html .=  '</ul>';
        $html .= '</div>';
        return $html;
    }
}
