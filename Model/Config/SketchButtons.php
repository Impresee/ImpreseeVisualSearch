<?php
/**
 * To upload files
 */
namespace Impresee\ImpreseeVisualSearch\Model\Config;

class SketchButtons implements \Magento\Framework\Option\ArrayInterface
{
  /**
   * Return array of options as value-label pairs
   * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
   */
    public function toOptionArray()
    {
        return [['value' => 'left', 'label' => __('Left')], ['value' => 'right', 'label' => __('Right')],];
    }
}
