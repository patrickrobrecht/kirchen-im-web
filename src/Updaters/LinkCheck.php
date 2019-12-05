<?php

namespace KirchenImWeb\Updaters;

/**
 * Utility for checking the availability of the website with the given URL.
 *
 * @package KirchenImWeb\Updaters
 */
class LinkCheck
{
    private $httpStatusCode;
    private $redirectTarget;

    public const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0';

    /**
     * Checks the availability of the website with the given URL.
     *
     * @param string $url the URL to check
     */
    public function __construct(string $url)
    {
        // Check with HEAD request.
        $this->check($url, true);
        if ($this->httpStatusCode > 400) {
            // Check with GET request to deal with servers not denying HEAD requests.
            $this->check($url, false);
        }
    }

    /**
     * Calls the given URL with curl.
     *
     * @param string $url the URL to check
     * @param bool $useHead true for a HEAD request, false for a GET request
     */
    private function check(string $url, $useHead = true)
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_NOBODY, $useHead);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_exec($handle);
        $this->httpStatusCode = (int)curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $this->redirectTarget = (string)curl_getinfo($handle, CURLINFO_REDIRECT_URL);
        curl_close($handle);
    }

    /**
     * Returns the HTTP status code.
     *
     * @return int the status code
     */
    public function getStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Returns the target URL of a redirect, otherwise the empty string.
     *
     * @return string the target URL
     */
    public function getRedirectTarget()
    {
        return $this->redirectTarget;
    }
}
