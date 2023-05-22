<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;

class SaveImpreseeConfigObserver implements ObserverInterface
{
    const API_REQUEST_URI = '';
    const API_REQUEST_ENDPOINT = '/register-magento-api/';
    protected $_logger;
    protected $_codesHelper;
    private $_responseFactory;
    private $_clientFactory;

    public function __construct(LoggerInterface $logger, CodesHelper $codes, ClientFactory $clientFactory,
        ResponseFactory $responseFactory
    ) {
        $this->_clientFactory = $clientFactory;
        $this->_responseFactory = $responseFactory;
        $this->_logger = $logger;
        $this->_codesHelper = $codes;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $impresee_app = $this->_codesHelper->getImpreseeUuid(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $consumer_key = $this->_codesHelper->getConsumerKey(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $consumer_secret = $this->_codesHelper->getConsumerSecret(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $access_token = $this->_codesHelper->getAccessToken(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $access_token_secret = $this->_codesHelper->getAccessTokenSecret(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (!$impresee_app || !$access_token) return;
            
            $response = $this->doJsonRequest(static::API_REQUEST_ENDPOINT . $impresee_app, array(
                'consumer_key' => $consumer_key,
                'consumer_secret' => $consumer_secret,
                'access_token' => $access_token,
                'access_token_secret' => $access_token_secret
            ));
            $status = $response->getStatusCode(); // 200 status code
            $responseBody = $response->getBody();
            $responseContent = $responseBody->getContents(); // here you will have the API response in JSON format

        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }


     /**
     * Do API request with provided params
     *
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     *
     * @return Response
     */
    private function doJsonRequest(
        string $uriEndpoint,
        array $body = []
    ): Response {
        /** @var Client $client */
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI,
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]]);

        try {
            $response = $client->post(
                $uriEndpoint, 
                [
                    GuzzleHttp\RequestOptions::JSON => $body
                ]
            );
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }
    

}