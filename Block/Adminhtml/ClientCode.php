<?php
/**
 *   Display client code in system configuration page
 */
namespace Impresee\ImpreseeVisualSearch\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Impresee\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use Magento\Backend\Block\Template\Context;

class ClientCode extends Field
{
    /**
     *   To fetch client code from db
     * @var CodesHelper
     */
    public $codesHelper;
    /**
     * @param Context
     * @param Impresee\ImpreseeVisualSearch\Helper\Codes
     */
    public function __construct(
        Context $context,
        CodesHelper $helper
    ) {
        $this->codesHelper = $helper;
        parent::__construct($context);
    }
    /**
     * Displays clientCode in system config
     * @param AbstractElement
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html  = '<tr>';
        $html .= '  <td class="label">Impresee DataFeed Client Code</td>';
        $html .= '  <td class="value" style="color:red">';
        $html .=       $this->codesHelper->getClientCode();
        $html .= '     <p class="note">';
        $html .= '         <span>Needed to show DataFeed</span>';
        $html .= '     </p>';
        $html .= '  </td>';
        $html .= '  <td></td>';
        $html .= '</tr>';
        return $html;
    }
}
