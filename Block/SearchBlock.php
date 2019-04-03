<?php
/**
 *  To fetch config data from DB and pass it to view (impresee.phtml)
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

use ImpreseeAI\ImpreseeVisualSearch\Helper\Panel as PanelHelper;

class SearchBlock extends Template
{
    /**
     * Const with the path to Impresee icons
     * @const string
     */
    const ICONS_PATH = "https://api.impresee.com/icons/";
    /**
     * To store and get stored data
     * @var Magento\Framework\Registry
     */
    public $registry;
    /**
     * App code for search in impresee
     * @var string
     */
    public $application_uuid;
    /**
     *  To get frontend config data
     * @var ImpreseeAI\ImpreseeVisualSearch\Helper\Panel
     */
    public $panelHelper;

    /**
     *  Path to media folder
     * @var string
     */
    public $mediaIconPath;
    /**
     * URL to media folder
     * @var string
     */
    public $mediaIconUrl;
    /**
     * Filesystem instance
     * @var Magento\Framework\Filesystem
     */
    protected $_filesystem;
    /**
     * @param Context $context
     * @param array $data
     * @param Magento\Framework\Registry $registry
     * @param ImpreseeAI\ImpreseeVisualSearch\Helper\Panel $panelHelper
     */
    public function __construct(
        Context $context,
        array $data,
        Registry $registry,
        PanelHelper $panelHelper
    ) {
        parent::__construct($context, $data);
        $this->registry =$registry;
        $this->panelHelper = $panelHelper;
        if (null !== ($registry->registry('searchResults'))) {
            $this->application_uuid = $registry->registry('searchResults')['application_uuid'];
        }
        $this->mediaIconPath   = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . "/icons";
        $this->mediaIconUrl   = $this->getUrl('pub/media') . "/icons";
    }
    /**
     * Define the url path to sketch icon
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getSketchIcon($store)
    {
        $sketchConfig = $this->panelHelper->getSketchIconRelativePath($store);
        if (is_file($this->mediaIconPath. "/" . $sketchConfig)) {
            return $this->mediaIconUrl . "/" . $sketchConfig;
        } else {
            $iconName = $this->panelHelper->getSketchIconName($store);
            if (strlen($iconName) == 0) {
                return $this::ICONS_PATH . "sketch1.svg";
            } else {
                return $this::ICONS_PATH . $iconName;
            }
        }
    }
    /**
     * Define the url path to photo icon
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE $store
     * @return string
     */
    public function getPhotoIcon($store)
    {
        $photoConfig = $this->panelHelper->getPhotoIconRelativePath($store);
        if (is_file($this->mediaIconPath. "/" . $photoConfig)) {
            return $this->mediaIconUrl . "/" . $photoConfig;
        } else {
            $iconName = $this->panelHelper->getPhotoIconName($store);
            if (strlen($iconName) == 0) {
                return $this::ICONS_PATH . "photo1.svg";
            } else {
                return $this::ICONS_PATH . $iconName;
            }
        }
    }
}
