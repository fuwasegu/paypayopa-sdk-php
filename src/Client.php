<?php

namespace PayPay\OpenPaymentAPI;

require_once('loader.php');

use PayPay\OpenPaymentAPI\Controller\Code;
use PayPay\OpenPaymentAPI\Controller\Payment;
use PayPay\OpenPaymentAPI\Controller\User;
use PayPay\OpenPaymentAPI\Controller\Wallet;
use PayPay\OpenPaymentAPI\Controller\Refund;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;

class ClientException extends Exception
{
}

class Client
{
    /**
     * Store auth credentials
     *
     * @var array
     */
    private $auth;
    /**
     * Stores client config
     *
     * @var array
     */
    private $config;
    /**
     * api endpoints
     *
     * @var array
     */
    private $endpoints;
    /**
     * api endpoint versions
     *
     * @var array
     */
    private $versions;
    /**
     * Guzzle client to handle http calls
     *
     * @var GuzzleHttpClient
     */
    private $requestHandler;
    /**
     * Instance of code controller
     *
     * @var Code
     */
    public $code;
    /**
     * Payment Controller
     *
     * @var Payment
     */
    public $payment;
    /**
     * Refund Controller
     *
     * @var Refund
     */
    public $refund;
    /**
     * User Controller
     *
     * @var User
     */
    public $user;
    /**
     * Wallet Controller
     *
     * @var Wallet
     */
    public $wallet;

    /**
     * Initialize a Client object with session,
     * optional auth handler, and options      *
     * @param Array $auth API credentials
     * @param boolean $productionmode Sandbox environment flag
     * @param GuzzleHttpClient|boolean $requestHandler
     */
    public function __construct($auth = null, $productionmode = false, $requestHandler = false)
    {
        if (!isset($auth['API_KEY']) || !isset($auth['API_SECRET'])) {
            throw new Exception("Invalid auth credentials", 1);
        }
        $this->auth = $auth;
        $toStg = !$productionmode ? '-stg' : '';
        $toStg = $productionmode == 'test' ? '-test' : $toStg;
        require("conf/config${toStg}.php");
        /** @phpstan-ignore-next-line */
        $this->config = $config;
        require('conf/endpoints.php');
        /** @phpstan-ignore-next-line */
        $this->endpoints = $endpoint;
        require("conf/apiVersions.php");
        /** @phpstan-ignore-next-line */
        $this->versions = $versions;

        if (!$requestHandler) {
            $this->requestHandler = new GuzzleHttpClient(['base_uri' => $this->config["API_URL"]]);
        } else {
            if (gettype($requestHandler) === 'GuzzleHttpClient' || gettype($requestHandler) === 'GuzzleHttp/Client') {
                /** @phpstan-ignore-next-line */
                $this->requestHandler = $requestHandler;
            } else {
                throw new ClientException('Invalid request handler', 500);
            }
        }
        $this->code = new Code($this, $auth);
        $this->payment = new Payment($this, $auth);
        $this->refund = new Refund($this, $auth);
        $this->user = new User($this, $auth);
        $this->wallet = new Wallet($this, $auth);
    }

    /**
     * Returns relevant config value
     *
     * @param string $configName Name of configuration
     * @return mixed
     */
    public function GetConfig($configName)
    {
        return $this->config[$configName];
    }

    /**
     * Returns relevant endpoint path
     *
     * @param String $endpointName Name of Endpoint
     * @return String
     */
    public function GetEndpoint($endpointName)
    {
        return $this->endpoints[$endpointName];
    }
    /**
     * Returns relevant endpoint version
     *
     * @param String $endpointName Name of Endpoint
     * @return String
     */
    public function GetEndpointVersion($endpointName)
    {
        return $this->versions[$endpointName];
    }
    /**
     * Returns Merchant ID
     *
     * @return string|boolean
     */
    public function GetMid()
    {
        if (isset($this->auth['MERCHANT_ID'])) {
            return $this->auth['MERCHANT_ID'];
        } else {
            return false;
        }
    }

    /**
     * get client request handler for controller
     *
     * @return GuzzleHttpClient
     */
    public function http()
    {
        return $this->requestHandler;
    }
}
