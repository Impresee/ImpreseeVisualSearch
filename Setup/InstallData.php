<?php
/**
 *  Generates an aleatory client_code on installation
 */
namespace Impresee\ImpreseeVisualSearch\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Config\Model\ResourceModel\Config;

class InstallData implements InstallDataInterface
{
  /**
   * To access to core_config_data
   * @var Magento\Config\Model\ResourceModel\Config
   */
    public $config;
  /**
   * Constructor
   * @param Magento\Config\Model\ResourceModel\Config $config
   */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }
  /**
   *  Generate client code on installation, and save it on db in table
   *  "core_config_data"
   * @param ModuleDataSetupInterface $setup
   * @param ModuleContextInterface   $context
   */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $clientCode = '';
        $chars = str_split("abcdefghijklmnopqrstuvwxyz0123456789");
        for ($i = 0; $i < 16; $i++) {
            $clientCode .= $chars[array_rand($chars)];
        }
        $key = 'impresee/general/client_code';
        $this->config->saveConfig($key, $clientCode, 'default', 0);
    }
}
