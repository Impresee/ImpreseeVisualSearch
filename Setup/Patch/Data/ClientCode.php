<?php


namespace ImpreseeAI\ImpreseeVisualSearch\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Config\Model\ResourceModel\Config;

class ClientCode
    implements DataPatchInterface,
    PatchRevertableInterface
{

    /**
   * To access core_config_data
   * @var Magento\Config\Model\ResourceModel\Config
   */
    public $config;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Magento\Config\Model\ResourceModel\Config $config
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Config $config
    ) {
        /**
         * If before, we pass $setup as argument in install/upgrade function, from now we start
         * inject it with DI. If you want to use setup, you can inject it, with the same way as here
         */
        $this->moduleDataSetup = $moduleDataSetup;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $clientCode = '';
        $chars = str_split("abcdefghijklmnopqrstuvwxyz0123456789");
        for ($i = 0; $i < 16; $i++) {
            $clientCode .= $chars[array_rand($chars)];
        }
        $key = 'impresee/general/client_code';
        $this->config->saveConfig($key, $clientCode, 'default', 0);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert(){
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        /**
         * This is dependency to another patch. Dependency should be applied first
         * One patch can have few dependencies
         * Patches do not have versions, so if in old approach with Install/Ugrade data scripts you used
         * versions, right now you need to point from patch with higher version to patch with lower version
         * But please, note, that some of your patches can be independent and can be installed in any sequence
         * So use dependencies only if this important for you
         */
        return [
        ];
    }


    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        /**
         * This internal Magento method, that means that some patches with time can change their names,
         * but changing name should not affect installation process, that's why if we will change name of the patch
         * we will add alias here
         */
        return [];
    }
}
