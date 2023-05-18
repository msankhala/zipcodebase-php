<?php

namespace Msankhala\ZipcodebasePhp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use RuntimeException;

/**
 * Class that will connect with zipcodebase api.
 */
class ZipcodebaseClient
{
    private $apiKey;
    private $apiBase;
    private $httpClient;

    public function __construct($apiKey, $apiBase = 'https://app.zipcodebase.com/api/v1')
    {
        $this->apiKey = $apiKey;
        $this->apiBase = rtrim($apiBase, '/') . '/';

        // Create a Guzzle HTTP client with the Guzzle7 adapter
        $this->httpClient = new Client([
            'http_errors' => false,
            'adapter' => new GuzzleAdapter()
        ]);
    }

    /**
     * Get location information by postal code
     * @param mixed $postalCodes
     *  A comma-separated list of postal codes.
     * @param string $countryCode
     *  The ISO 3166-1 alpha-2 country code. Default is US.
     * @return mixed The array of location information.
     */
    public function getPostalCodeInfo($postalCodes, $countryCode = 'US')
    {
        $endpoint = $this->apiBase . 'search?' . http_build_query([
            'codes' => $postalCodes,
            'country' => $countryCode
        ]);
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Calculate distance between two postal codes
     * @param mixed $postalCode The postal code to calculate distance from.
     * @param string $compare The comma-separated list of postal codes to
     *  calculate distance to. Max 100 values.If multiple postal codes are
     *  submitted to compare, ensure they are within the same country. If you
     *  need to search postal codes across different countries, please submit
     *  a separate API request. Example: 10 submitted postal codes will cost
     * 10 API credits.
     * @param string $countryCode The ISO 3166-1 alpha-2 country code. Default
     *  is US.
     * @param string $unit The unit of distance can be 'km' or 'miles. Default
     *  is km.
     * @return mixed The array of distance information.
     */
    public function calculateDistance($postalCode, $compare, $countryCode = 'US', $unit = 'km')
    {
        $endpoint = $this->apiBase . 'distance?' . http_build_query([
            'code' => $postalCode,
            'compare' => $compare,
            'country' => $countryCode,
            'unit' => $unit
        ]);
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get postal codes within a radius.
     * @param mixed $postalCode The postal code to calculate distance from.
     * @param mixed $radius The radius to search within. Max 500.
     * @param string $countryCode The ISO 3166-1 alpha-2 country code. Default
     *  is US.
     * @param string $unit The unit of distance can be 'km' or 'miles. Default
     *  is km.
     * @return mixed The array of postal codes within the radius.
     */
    public function getPostalCodesWithinRadius($postalCode, $radius, $countryCode = 'US', $unit = 'km')
    {
        $endpoint = $this->apiBase . 'radius?' . http_build_query([
            'code' => $postalCode,
            'radius' => $radius,
            'country' => $countryCode,
            'unit' => $unit
        ]);
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get postal codes within a distance.
     * @param mixed $postalCodes A comma-separated list of postal codes.
     * @param mixed $distance The distance to search within. Max 500.
     * @param string $countryCode The ISO 3166-1 alpha-2 country code. Default
     *  is US.
     * @param string $unit The unit of distance can be 'km' or 'miles. Default
     *  is km.
     * @return mixed
     */
    public function getPostalCodesWithinDistance($postalCodes, $distance, $countryCode = 'US', $unit = 'km')
    {
        $endpoint = $this->apiBase . 'match?' . http_build_query([
            'codes' => $postalCodes,
            'distance' => $distance,
            'country' => $countryCode,
            'unit' => $unit
        ]);
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get postal codes by city.
     * @param string $city The city name.
     * @param string $countryCode The ISO 3166-1 alpha-2 country code. Default
     *  is US.
     * @param string $stateName The two letter state code of the province. List
     * of provinces for a country can be retrieved using our province endpoint.
     * @param int $limit The number of postal codes to return.
     * @return mixed The array of postal codes.
     */
    public function getPostalCodesByCity($city, $countryCode = 'US', $stateName = null, $limit = 100)
    {
        $endpoint = $this->apiBase . 'code/city?' . http_build_query([
            'city' => $city,
            'country' => $countryCode,
            'state_name' => $stateName,
            'limit' => $limit
        ]);
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get postal codes by state.
     * @param string $stateName The name of the province. List of provinces for
     *  a country can be retrieved using our province endpoint.
     * @param string $countryCode The ISO 3166-1 alpha-2 country code. Default
     *  is US.
     * @param int $limit The number of postal codes to return.
     * @return mixed The array of postal codes.
     */
    public function getPostalCodesByState($stateName, $countryCode = 'US', $limit = 100)
    {
        $endpoint = $this->apiBase . 'code/state?' . http_build_query([
            'state_name' => $stateName,
            'country' => $countryCode,
            'limit' => $limit
        ]);
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get the list of states/provinces for a country.
     * @param string $countryCode The ISO 3166-1 alpha-2 country code. Default
     *  is US.
     * @return string The array of states/provinces.
     */
    public function getStatesByCountry($countryCode = 'US')
    {
        $endpoint = $this->apiBase . 'country/province?' . http_build_query([
            'country' => $countryCode,
        ]);
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get the remaining credits for this account.
     * @return mixed The array of remaining credits.
     */
    public function getCredits()
    {
        $endpoint = $this->apiBase . 'status';
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Make the API request.
     * @param string $method The HTTP method.
     * @param string $endpoint The API endpoint.
     * @return mixed The API response.
     */
    private function makeRequest($method, $endpoint)
    {
        try {
            $response = $this->httpClient->request($method, $endpoint, [
                'headers' => [
                    'apikey' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                return json_decode($body, true);
            } else {
                // Handle error response
                return [
                    'error' => $body,
                    'statusCode' => $statusCode
                ];
            }
        } catch (GuzzleException $e) {
            // Handle exception
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
