<?php

namespace KirchenImWeb\Helpers;

use Exception;
use OpenCage\Geocoder\Geocoder;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ParameterChecker
 *
 * @package KirchenImWeb\Helpers
 */
class ParameterChecker extends AbstractHelper
{

    public function extractFilterParameters(Request $request): array
    {
        $data = $request->getQueryParams();
        $filters = [];
        $filters['ids'] = isset($data['ids']) ? $this->toIntArray($data['ids']) : [];
        $filters['parent'] = isset($data['parent']) ? (int)$data['parent'] : 0;
        $filters['name'] = isset($data['name']) ? trim($data['name']) : '';
        $filters['postalCode'] =
            isset($_GET['postalCode']) && (int)$_GET['postalCode'] > 0 ? $_GET['postalCode'] : '';
        $filters['city'] = isset($_GET['city']) ? trim($_GET['city']) : '';
        $filters['country'] = isset($_GET['countryCode']) ? trim($_GET['countryCode']) : '';
        $filters['denomination'] = isset($_GET['denomination']) ? trim($_GET['denomination']) : '';
        $filters['type'] = isset($_GET['type']) ? trim($_GET['type']) : '';
        $filters['hasWebsiteType'] = isset($_GET['hasWebsiteType']) ? trim($_GET['hasWebsiteType']) : '';
        $filters['options'] = $this->extractOptions($data['options'] ?? '');
        return $filters;
    }

    public function extractOptions($optionString): array
    {
        $optionArray = explode(',', $optionString);
        $options = [];
        foreach (['childrenRecursive', 'includeSelf'] as $option) {
            $options[$option] = in_array($option, $optionArray, true);
        }
        return $options;
    }

    private function toIntArray($s)
    {
        $array = explode(',', $s);
        $intArray = [];
        foreach ($array as $a) {
            $i = (int)$a;
            if ($i > 0) {
                $intArray[] = $i;
            } else {
                return false;
            }
        }
        return $intArray;
    }

    public function extractFilterWebsites(Request $request)
    {
        $data = $request->getQueryParams();
        $websites = [];
        foreach (Configuration::getInstance()->websites as $websiteId => $websiteName) {
            if (
                (isset($data['hasWebsiteType']) && $data['hasWebsiteType'] === $websiteId)
                || (isset($data[$websiteId]) && $data[$websiteId] === 'show')
            ) {
                $websites[$websiteId] = $websiteName;
            }
        }
        // If no website type is selected, use default.
        if (sizeof($websites) === 0) {
            $websites = Configuration::getInstance()->preselectedWebsites;
        }
        return $websites;
    }

    public function extractSort(Request $request, $default = ''): array
    {
        $data = $request->getQueryParams();
        $sort = isset($data['sort']) ? trim($data['sort']) : $default;
        $sortColumnId = -1;
        $columns = Configuration::getInstance()->sortOptions;
        if (array_key_exists($sort, $columns)) {
            $sortColumnId = array_search($sort, array_keys($columns), true);
        }
        return [
            'name' => ($sortColumnId > -1) ? $sort : '',
            'id' => $sortColumnId,
            'dir' => ($sortColumnId >= 6) ? 1 : 0
        ];
    }

    public function parseAddFormPreSelectionParameters(Request $request)
    {
        $params = $request->getQueryParams();

        $data = [];
        if (isset($params['parentId'])) {
            $data['parentId'] = trim($_GET['parentId']);
        }
        if (isset($params['denomination'])) {
            $data['denomination'] = trim($_GET['denomination']);
        }
        if (isset($params['countryCode'])) {
            $data['countryCode'] = trim($_GET['countryCode']);
        }

        return $data;
    }

    public function parseAddFormParameters(Request $request): array
    {
        $post = $request->getParsedBody();

        // Parse the data.
        $keys = [
            'name',
            'street',
            'postalCode',
            'city',
            'countryCode',
            'denomination',
            'type',
            'parentId',
            'hasChildren'
        ];
        $data = [];
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = trim($post[$key]);
            } else {
                $data[$key] = '';
            }
        }
        // Check the data.
        $dataCorrect = true;
        $messages = [];
        if ($this->isNullOrEmptyString($data['name'])) {
            $dataCorrect = false;
            $messages[]  = _('Bitte einen gültigen Namen angeben!');
        }
        if ($this->isNullOrEmptyString($data['street'])) {
            $dataCorrect = false;
            $messages[]  = _('Bitte die Straße angeben!');
        }
        if ($this->isNullOrEmptyString($data['city'])) {
            $dataCorrect = false;
            $messages[]  = _('Bitte einen Ort angeben!');
        }
        if (!$this->isCountryCode($data['countryCode'])) {
            $dataCorrect = false;
            $messages[]  = _('Bitte ein Land auswählen!');
        }
        if (!$this->isPostalCode($data['postalCode'], $data['countryCode'])) {
            $dataCorrect = false;
            $messages[]  = _('Bitte eine Postleitzahl angeben!');
        }
        if (!$this->isDenomination($data['denomination'])) {
            $dataCorrect = false;
            $messages[]  = _('Bitte eine Konfession auswählen!');
        }
        if (!$this->isType($data['type'])) {
            $dataCorrect = false;
            $messages[]  = _('Bitte einen Gemeindetyp auswählen!');
        }
        if (!$this->isParentId($data['parentId'])) {
            $dataCorrect = false;
            $messages[]  = _('Bitte keine oder eine gültige nächsthöhere Ebene auswählen!');
        }
        if ($data['hasChildren'] === 'on') {
            $data['hasChildren'] = 1;
        } else {
            $data['hasChildren'] = 0;
        }

        if ($dataCorrect) {
            $geolocation = self::getInstance()->getGeolocation(
                $data['street'],
                $data['city'],
                $data['countryCode']
            );
            $data['lat'] = $geolocation['lat'];
            $data['lon'] = $geolocation['lon'];
        }

        // Parse URLs.
        $c = Configuration::getInstance();
        $urls = [];
        $urlsCorrect = true;
        foreach ($c->websites as $website_id => $websiteName) {
            if (isset($_POST[$website_id . 'URL'])) {
                $url = trim($_POST[$website_id . 'URL']);
                if ($url !== '') {
                    if ($this->isValidURL($url, $c->websitesStartOfURL[$website_id])) {
                        $urls[$website_id] = $url;
                    } else {
                        // a submitted URL is invalid.
                        $messages[]  = sprintf(
                            _('Bitte eine gültige oder keine URL für %s angeben, diese muss mit %s beginnen.'),
                            $c->websites[$website_id],
                            $c->websitesStartOfURL[$website_id]
                        );
                        $urlsCorrect = false;
                    }
                } // else no url submitted which is fine.
            }
        }
        $data['urls'] = $urls;

        return [
            'data' => $data,
            'messages' => $messages,
            'correct' => $dataCorrect && $urlsCorrect
        ];
    }

    /**
     * Tests whether the given variable is null or the empty string.
     *
     * @param string $str
     * @return boolean
     */
    private function isNullOrEmptyString($str): bool
    {
        return (!isset($str) || trim($str) === '');
    }

    /**
     * Tests whether the given variable is a valid postal code for the given country.
     *
     * @param number|string $postalCode
     * @param string $countryCode
     * @return number
     */
    private function isPostalCode($postalCode, $countryCode)
    {
        switch ($countryCode) {
            case 'DE':
                return preg_match('/[0-9]{5}/', $postalCode);
                break;
            case 'AT':
            case 'CH':
            case 'LI':
            case 'LU':
                return preg_match('/[0-9]{4}/', $postalCode);
                break;
        }
        return 0;
    }

    /**
     * Tests whether the given variable is a valid country code.
     *
     * @param string $countryCode
     * @return boolean
     */
    private function isCountryCode($countryCode): bool
    {
        return !$this->isNullOrEmptyString($countryCode)
               && array_key_exists($countryCode, Configuration::getInstance()->countries);
    }

    /**
     * Tests whether the given variable is a valid denomination.
     *
     * @param string $denomination
     * @return boolean
     */
    private function isDenomination($denomination): bool
    {
        return !$this->isNullOrEmptyString($denomination)
               && array_key_exists($denomination, Configuration::getInstance()->denominations);
    }

    /**
     * Tests whether the given variable is a valid type.
     *
     * @param string $type
     * @return boolean
     */
    private function isType($type): bool
    {
        return !$this->isNullOrEmptyString($type) && array_key_exists($type, Configuration::getInstance()->types);
    }

    /**
     * Tests whether the given variable is a valid parent id.
     *
     * @param number $parentId
     * @return boolean
     */
    private function isParentId($parentId): bool
    {
        return $parentId === 'none' || Database::getInstance()->getEntry($parentId) !== false;
    }

    /**
     * Tests whether the given variable is a valid URL.
     *
     * @param string $url
     * @return boolean
     */
    private function isURL($url): bool
    {
        return ($url && is_string($url) && $url !== ''
                && preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url));
    }

    /**
     * Tests whether the given variable is a valid URL with begins with the given string.
     *
     * @param string $url
     * @param string $startsWith
     * @return boolean
     */
    private function isValidURL($url, $startsWith = ''): bool
    {
        return $this->isURL($url) && $this->startsWith($url, $startsWith);
    }

    /**
     * Tests whether the given haystack starts with needle.
     *
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    private function startsWith($haystack, $needle): bool
    {
        // search backwards starting from haystack length characters from the end
        return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    private function getGeolocation($street, $city, $countryCode): array
    {
        $geocoder = new Geocoder(OPENCAGE_API_KEY);
        try {
            $result = $geocoder->geocode(
                $street . ', ' . $city,
                [
                    'countrycode' => strtolower(Configuration::getInstance()->countries[$countryCode])
                ]
            );

            if ($result && $result['total_results'] > 0) {
                $first = $result['results'][0];
                return [
                    'lat' => $first['geometry']['lat'],
                    'lon' => $first['geometry']['lng']
                ];
            }
        } catch (Exception $e) {
        }

        return [
            'lat' => null,
            'lon' => null
        ];
    }
}
