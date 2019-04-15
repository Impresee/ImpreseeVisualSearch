<?php
/**
 *  Generate datafeed xml with Impresee format
 */

namespace ImpreseeAI\ImpreseeVisualSearch\Model;

use ImpreseeAI\ImpreseeVisualSearch\Model\Products as ProductCollection;
use \Magento\Catalog\Model\Category as Category;
use \Magento\Store\Model\App\Emulation;
use \Magento\Store\Model\StoreManagerInterface;

class GenerateXml
{
  /**
   * Product features saved on the xml file
   * @var string[]
   */
    protected $PRODUCT_ATTRIBUTES = ["name"];
  /**
   * Max ammount of images added to the xml file
   * @const int
   */
    const AMOUNT_IMAGES = 1;
  /**
   * Collection of Products
   * @var ImpreseeAI\ImpreseeVisualSearch\Model\Products
   */
    protected $_productCollection;
  /**
   * To load categories
   * @var Magento\Catalog\Model\Category
   */
    protected $_category;
  /**
   * To emulate a particulary Store
   * @var Magento\Store\Model\App\Emulation
   */
    protected $_appEmulation;
  /**
   * Store context
   * @var \Magento\Store\Model\StoreManagerInterface
   */
    protected $_storeManagerInterface;
  /**
   * Root category of Magento
   * @var Magento\Catalog\Model\Category
   */
    protected $_rootCategory;

  /**
   * Constructor
   * @var ImpreseeAI\ImpreseeVisualSearch\Model\Products $ProductCollection
   * @var Magento\Catalog\Model\Category $category
   * @var Magento\Store\Model\App\Emulation $appEmulation
   * @var Magento\Store\Model\StoreManagerInterface $storeManagerInterface
   */
    public function __construct(
        ProductCollection $Collection,
        Category $category,
        Emulation $appEmulation,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->_productCollection = $Collection;
        $this->_category = $category;
        $this->_rootCategory = $category;
        $this->_appEmulation = $appEmulation;
        $this->_storeManagerInterface = $storeManagerInterface;
    }
  /**
   * Convert an array of strings to an array of ints
   * (useful to generate catalog collections)
   * @param array of string (numbers of ids)
   * @return array of int (numbers of ids)
   */
    public function toIntArray($stringArray)
    {
        return array_map(function($val) { return (int)$val; },
          $stringArray);
    }
  /**
   * Main XML generation function
   * @param store id (int)
   * @return string (with all products on Impresee xml format)
   */
    public function generateXmlByStore($store)
    {
        $resultString = "";
        $initialEnvironmentInfo = $this->_appEmulation
        ->startEnvironmentEmulation($store);
        $collection = $this->_productCollection
        ->getCollection()
        ->setStore($store)
        ->addStoreFilter($store)
        ->addAttributeToSelect($this->PRODUCT_ATTRIBUTES)
        ->addMediaGalleryData();
        $resultString = $this->getXml($collection);
        $this->_appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        return $resultString;
    }
  /**
   * Return a string with the datafeed on a XML file with Impresee schemma
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products Collection $products collection of products
   * @return string (XML like)
   */
    public function getXml($products)
    {
        $resultString  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>";
        $resultString .= "<catalog>";
        $resultString .= "<products>";
        $resultString .= $this->makeProductsTags($products);
        $resultString .= "</products>";
        $resultString .= "<types>";
        $resultString .= $this->makeTypeTags($products);
        $resultString .= "</types>";
        $resultString .= "</catalog>";
        return $resultString;
    }
  /**
   * Make XML products tags for all product according to Impresee Schema
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products Collection $products collection of products
   * @return string (XML like)
   */
    public function makeProductsTags($products)
    {
        $resultString = "";
        foreach ($products as $product) :
            {
            $resultString .= "<product code=\"".$product->getSku()."\" url=\"".$product->getProductUrl()."\">";

            $resultString .= "<categories>";
            $resultString .= $this->makeCategoriesTags($product);
            $resultString .= "</categories>";

            $resultString .= "<texts>";
            $resultString .= $this->makeAttributesTags($product);
            $resultString .= "</texts>";

            $resultString .= "<images>";
            $resultString .= $this->makeImageTags($product);
            $resultString .= "</images>";

            $resultString .= "</product>";
            }
        endforeach;
        return $resultString;
    }
  /**
   * Make categories XML tags for a single product according to Impresee XML
   * schemma
   * @param Magento\Catalog\Model\Product
   * @return string (XML like)
   */
    private function makeCategoriesTags($product)
    {
        $resultString = "";
        foreach ($product->getCategoryIds() as $category) :
            {
            if ($category) {
                $resultString .= "<category ref_code=\"".$category."\"/>";
            }
            }
        endforeach;
        return $resultString;
    }
  /**
   * Make text XML tags for a single product according to Impresee XML schema
   * @param Magento\Catalog\Model\Product
   * @return string (XML like)
   */
    private function makeAttributesTags($product)
    {
        $resultString = "";

        foreach ($this->PRODUCT_ATTRIBUTES as $attribute) :
            {
            if ($info=$product->getData($attribute)) {
                $resultString .= "<text ref_code=\"".$attribute."\">".htmlentities(strip_tags($info), ENT_XML1, "UTF-8")."</text>";
            }
            }
        endforeach;
        return $resultString;
    }
  /**
   * Make image XML tags for a single product according to Impresee XML schema
   * @param Magento\Catalog\Model\Product
   * @return string (XML like)
   */
    private function makeImageTags($product)
    {
        $resultString = "";
        $images = $this->getImageUrl($product);
        $count = 1;
        foreach ($images as $image) :
            {
            if ($count <= $this::AMOUNT_IMAGES) {
                $resultString .= "<image ref_code=\"image".$count."\" url_image=\"".strip_tags($image['url'])."\"/>";
                $count++;
                if ($count > $this::AMOUNT_IMAGES) {
                    return $resultString;
                }
            } else {
                $resultString .= "<image ref_code=\"extraImage\" url_image=\"".strip_tags($image['url'])."\"/>";
            }
            }
        endforeach;

        return $resultString;
    }
  /**
   * Creates types XML tags according Impresee XML schemma
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products Collection $products collection of products
   * @return string (XML like)
   */
    private function makeTypeTags($products)
    {
        $resultString  = "<categories>";
        $resultString .= $this->makeHierarchyCategoriesTags($products);
        $resultString .= "</categories>";

        $resultString .= "<texts>";
        $resultString .= $this->makeValidTextsTag();
        $resultString .= "</texts>";

        $resultString .= "<images>";
        $resultString .= $this->makeValidImagesTag();
        $resultString .= "</images>";
        return $resultString;
    }
  /**
   * Make hierarchy categories xml tags according to Impresee XML schemma
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products Collection $products collection of products
   * @return string (XML like)
   */
    private function makeHierarchyCategoriesTags($products)
    {
        $resultString = "";
        $store = $this->_storeManagerInterface->getStore();
        $rootCategoryId = $store->getRootCategoryId();
        $rootCategory = $this->_rootCategory->load($rootCategoryId);
        $categoriesIdArray = $this->toIntArray($rootCategory->getAllChildren(true));
        $resultString .= "<category code=\"".$rootCategoryId."\" name=\"".$rootCategory->getName()."\"/>";
        foreach ($categoriesIdArray as $categoryId) :
            {
            /** ignores root category (already added)*/
            if ($rootCategoryId == $categoryId) {
                continue;
            }
            $category = $this->_category->load($categoryId);
            $name = htmlspecialchars(strip_tags($category->getName()));
            $resultString .= "<category code=\"".$category->getId()."\" name=\"".$name."\">";
            $resultString .= "<parent ref_code=\"".$category->getParentId()."\"/>";
            $resultString .= "</category>";
            }
        endforeach;
        return $resultString;
    }
  /**
   * Make text xml types tags according to Impresee XML schemma
   * @return string (XML like)
   */
    private function makeValidTextsTag()
    {
        $resultString = "";
        foreach ($this->PRODUCT_ATTRIBUTES as $attribute) :
            {
            $resultString .= "<text code=\"".$attribute."\" name=\"".str_replace("_", " ", $attribute)."\"/>";
            }
        endforeach;
        return $resultString;
    }
  /**
   * Make image xml types tags according to Impresee XML schemma
   * @return string (XML like)
   */
    private function makeValidImagesTag()
    {
        $resultString = "";
        $resultString .= "<image code=\"image1\" name=\"Main Image \"/>";
        for ($i = 2; $i <= $this::AMOUNT_IMAGES; $i++) {
            $resultString .= "<image code=\"image".$i."\" name=\"Image ".$i."\"/>";
        }
        $resultString .= "<image code=\"extraImage\" name=\"Extra Image\"/>";
        return $resultString;
    }
    /**
     * To get images from a single product
     * @param Magento\Catalog\Model\Product
     */

    protected function getImageUrl($product)
    {
        return $product->getMediaGalleryImages();
    }
}
