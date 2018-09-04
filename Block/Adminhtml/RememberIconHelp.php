<?php
/**
 * Display comments in system configuration page, icons section
 */
namespace Impresee\ImpreseeVisualSearch\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Backend system config datetime field renderer
 * @api
 * @since 100.0.2
 */
class RememberIconHelp extends Field
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html  =  '<i class="fas fa-exclamation-triangle"></i>To make the visual search available you must add your own buttons to your theme.</br>';
        $html .= 'Example: </br>';
        $html .= '<pre style="color:#000020;background:#f6f8ff;"><span style="color:#308080; ">&lt;</span><span style="color:#003060; ">div</span> class<span style="color:#308080; ">=</span><span style="color:#800000; ">"</span><span style="color:#1060b6; ">block block-search</span><span style="color:#800000; ">"</span><span style="color:#308080; ">></span>
          <span style="color:#308080; ">&lt;</span><span style="color:#003060; ">div</span> class<span style="color:#308080; ">=</span><span style="color:#800000; ">"</span><span style="color:#1060b6; ">block block-title</span><span style="color:#800000; ">"</span><span style="color:#308080; ">></span><span style="color:#308080; ">&lt;</span>strong<span style="color:#308080; ">></span><span style="color:#308080; ">&lt;</span><span style="color:#406080; ">?</span>php <span style="color:#595979; ">/* @escapeNotVerified */</span> echo __<span style="color:#308080; ">(</span><span style="color:#ffffff; background:#dd9999; font-weight:bold; font-style:italic; ">"Search"</span><span style="color:#308080; ">)</span><span style="color:#406080; ">;</span> <span style="color:#406080; ">?</span><span style="color:#308080; ">></span><span style="color:#308080; ">&lt;</span><span style="color:#308080; ">/</span>strong<span style="color:#308080; ">></span><span style="color:#308080; ">&lt;</span><span style="color:#308080; ">/</span><span style="color:#003060; ">div</span><span style="color:#308080; ">></span>
          <span style="color:#308080; ">&lt;</span><span style="color:#003060; ">div</span> class<span style="color:#308080; ">=</span><span style="color:#800000; ">"</span><span style="color:#1060b6; ">search-button</span><span style="color:#800000; ">"</span><span style="color:#308080; ">></span>
            <span style="color:#308080; ">&lt;</span>i class<span style="color:#308080; ">=</span><span style="color:#800000; ">"</span><span style="color:#1060b6; ">fa fa-edit impresee-sketch-button</span><span style="color:#800000; ">"</span> title<span style="color:#308080; ">=</span><span style="color:#800000; ">"</span><span style="color:#1060b6; ">Search by sketch</span><span style="color:#800000; ">"</span><span style="color:#308080; ">></span><span style="color:#308080; ">&lt;</span><span style="color:#308080; ">/</span>i<span style="color:#308080; ">></span>
            <span style="color:#308080; ">&lt;</span>i class<span style="color:#308080; ">=</span><span style="color:#800000; ">"</span><span style="color:#1060b6; ">fa fa-camera-retro impresee-photo-button</span><span style="color:#800000; ">"</span> title<span style="color:#308080; ">=</span><span style="color:#800000; ">"</span><span style="color:#1060b6; ">Search by photo</span><span style="color:#800000; ">"</span><span style="color:#308080; ">></span><span style="color:#308080; ">&lt;</span><span style="color:#308080; ">/</span>i<span style="color:#308080; ">></span>
          <span style="color:#308080; ">&lt;</span><span style="color:#308080; ">/</span><span style="color:#003060; ">div</span><span style="color:#308080; ">></span>
<span style="color:#308080; ">&lt;</span><span style="color:#308080; ">/</span><span style="color:#003060; ">div</span><span style="color:#308080; ">></span>
      </pre>';
        return $html;
    }
}
