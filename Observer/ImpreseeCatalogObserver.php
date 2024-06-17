<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */

namespace ImpreseeAI\ImpreseeVisualSearch\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\ResponseFactory;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use Magento\Catalog\Model\ProductRepository;

abstract class ImpreseeCatalogObserver implements ObserverInterface
{
    protected $_logger;
    protected $_codesHelper;
    protected $_responseFactory;
    protected $_clientFactory;
    protected $productRepository;

    const BASE_URL = 'https://console.impresee.com';
    const END_POINT = '/MagentoCatalogSynchronization/api/v1/';
    const END_POINT_FINAL = 'import/';
    const MAX_SIZE_LIST = 250;

    public function __construct(
        LoggerInterface $logger,
        CodesHelper $codes,
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory,
        ProductRepository $productRepository
    ) {
        $this->_clientFactory = $clientFactory;
        $this->_responseFactory = $responseFactory;
        $this->_logger = $logger;
        $this->_codesHelper = $codes;
        $this->productRepository = $productRepository;
    }

    protected function doMultipleRequest(string $endPoint, array $listOfIdentifiers)
    {
        try {
            $uuid = $this->getImpreseeCatalogUuid();

            $idsChunks = array_chunk($listOfIdentifiers, self::MAX_SIZE_LIST);
            foreach ($idsChunks as $chunk) {
                $listIds = implode(',', $chunk);
                $response = $this->doRequest("{$endPoint}/{$uuid}/$listIds");
                $this->_logger->debug($response->getBody()->getContents());
            }
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
    }

    /**
     * Do API request with provided params
     *
     * @param string $uriEndpoint
     *
     * @return Response
     */
    protected function doRequest(string $uriEndpoint, string $endPointFinal = null, ?array $jsonBody = null)
    {
        /** @var Client $client */
        $client = $this->_clientFactory->create(['config' => [
            'base_uri' => self::BASE_URL,
        ]]);

        if ($endPointFinal !== null){
            $fullUri = self::END_POINT . $endPointFinal  . $uriEndpoint;
        }
        else{
            $fullUri = self::END_POINT . self::END_POINT_FINAL . $uriEndpoint;
        }

        try {
            $options = [];
            if ($jsonBody !== null) {
                $options['headers'] = [
                    'Content-Type' => 'application/json'
                ];
                $options['json'] = $jsonBody;
            }
            $response = $client->post($fullUri, $options);
            $this->_logger->debug($response->getBody()->getContents());
            return $response;
        } catch (GuzzleException $e) {
            $this->_logger->error('GuzzleException in doRequest: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;  // Rethrow the exception to propagate it to the caller
        } catch (\Exception $e) {
            $this->_logger->error('Exception in doRequest: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;  // Rethrow the exception to propagate it to the caller
        }
    }

    protected function getProductSkuById($productId)
    {
        try {
            $product = $this->productRepository->getById($productId);
            return $product->getSku();
        } catch (\Exception $e) {
            $this->_logger->error('Error fetching SKU for Product ID ' . $productId . ': ' . $e->getMessage());
            return null;
        }
    }
    protected function getImpreseeAccessToken(){
        try {
            $uuid = $this->_codesHelper->getAccessToken(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (!$uuid) {
                throw new \Exception('Access token not found for the current store scope.');
            }
            return $uuid;
        } catch (\Exception $e) {
            $this->_logger->error('Error fetching access token: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function getImpreseeCatalogUuid()
    {
        try {
            $uuid = $this->_codesHelper->getImpreseeCatalogUuid(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (!$uuid) {
                throw new \Exception('UUID not found for the current store scope.');
            }
            return $uuid;
        } catch (\Exception $e) {
            $this->_logger->error('Error fetching Impresee Catalog UUID: ' . $e->getMessage());
            throw $e;
        }
    }
}

