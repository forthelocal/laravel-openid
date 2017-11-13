<?php

namespace ForTheLocal\Laravel\OpenID;

use Exception;
use GuzzleHttp\Client as HttpClient;


/**
 * Class Discovery
 *
 * OpenID Connect Discovery 1.0
 * http://openid.net/specs/openid-connect-discovery-1_0.html
 *
 * @package ForTheLocal\Laravel\OpenID
 */
class Discovery
{
    const DURATION_DEFAULT = 60 * 24; // 24 hours
    protected $primaryKey = 'issuer';
    protected $fillable = ['data'];

    private $jwt;
    private $data;

    // TODO enable cache the data in db by configuration with env.
    private $cacheEnabled = false;

    /**
     * Discovery constructor.
     * @param JWT $jwt
     * @param array $options
     * @throws Exception
     */
    function __construct(JWT $jwt, $options = [])
    {
        $this->jwt = $jwt;

        if (empty($options['data'])) {
            $this->data = json_decode($this->fetchConfiguration());
        } else {
            $this->data = json_decode($options['data']);
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function fetchConfiguration(): string
    {
        $client = $this->getConfiguredHttpClient();
        $res = $client->get('.well-known/openid-configuration');
        if ($res->getStatusCode() != 200) {
            throw new Exception('fail to connect to endpoint.');
        }

        return $res->getBody();
    }

    public function getJwksUri(): string
    {
        return $this->data->jwks_uri;
    }

    public function refreshCache()
    {
        // TODO
    }

    public function getConfiguredHttpClient()
    {
        $client = new HttpClient(["base_uri" => $this->jwt->getIssuer()]);

        return $client;
    }

}
