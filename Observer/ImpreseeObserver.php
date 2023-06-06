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
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

abstract class ImpreseeObserver implements ObserverInterface
{
    const API_REQUEST_ENDPOINT = '/register-magento-api/';
    protected $_logger;
    protected $_codesHelper;
    protected $_responseFactory;
    protected $_clientFactory;

    public function __construct(LoggerInterface $logger, 
        CodesHelper $codes, 
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory
    ) {
        $this->_clientFactory = $clientFactory;
        $this->_responseFactory = $responseFactory;
        $this->_logger = $logger;
        $this->_codesHelper = $codes;
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
    protected function doJsonRequest(
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