<?php

namespace Ubermanu\Flamingo\Helper;

use Analog\Analog;

/**
 * Class GoogleHelper
 * @package Ubermanu\Flamingo\Helper
 */
class GoogleHelper
{
    /**
     * Google geocode uri
     * @var string
     */
    const geocodeUri = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * Google API key
     * @var string
     */
    const apiKey = 'AIzaSyC9v0Z7vGXL7Qyv3wx7qelG44Aki82LsQo';

    /**
     * Use Google geocode system
     * - Uses last address line (if multi-line)
     *
     * @param array $data
     */
    public function geocode($data)
    {
        foreach (current($data) as &$record) {

            // Pause a bit
            sleep(1);

            // Fields to search for
            $fields = ['address', 'zip', 'city'];

            // Get values from record
            $locationData = array_intersect_key($record, array_flip($fields));

            // Get last value from address
//            $tmp = explode(PHP_EOL, $locationData['address']);
//            $locationData['address'] = array_pop($tmp);

            // Add country
            $locationData['country'] = 'France';

            // Set up parameters array
            $params = [
                'address' => implode(',', array_filter($locationData)),
                'apiKey' => self::apiKey,
            ];

            // Build request url
            $requestUri = self::geocodeUri . '?' . http_build_query($params);
            // Execute request
            if ($request = json_decode(file_get_contents($requestUri))) {

                // Check for results and existing location
                if (($result = current($request->results)) && $location = $result->geometry->location) {

                    // Set lon/lat on this record
                    $record['longitude'] = $location->lng;
                    $record['latitude'] = $location->lat;

                    Analog::debug(sprintf('Geocode: %s - %s', $record['title'], json_encode($location)));
                } else {

                    Analog::debug(sprintf('Geocode: %s - %s', $record['title'], $request->status));
                }
            }
        }
    }
}
