<?php

namespace GreekNameDays;

class GreekNameDays
{
    const USER_AGENT  = "GreekNameDays";
    const VERSION     = "1.0";
    const ENDPOINT    = "http://www.greeknamedays.gr/tools/api/";
    const LANGUAGE_GR = "gr";
    const LANGUAGE_EN = "en";

    private $options =
        [
            "u"      => null,
            "p"      => null,
            "langid" => self::LANGUAGE_GR
        ];

    public function __construct($username, $password, $language = self::LANGUAGE_GR)
    {
        if (!extension_loaded("curl")) throw new \RuntimeException("curl is not loaded.");

        $this->setUsername($username)
             ->setPassword($password)
             ->setLanguage($language);
    }

    public function setUsername($username)
    {
        $this->setOption("u", $username);

        return $this;
    }

    public function getUsername()
    {
        return $this->getOption("u");
    }

    public function setPassword($password)
    {
        $this->setOption("p", $password);

        return $this;
    }

    public function getPassword()
    {
        return $this->getOption("p");
    }

    public function setLanguage($language)
    {
        if (!in_array($language, [self::LANGUAGE_EN, self::LANGUAGE_GR])) $language = self::LANGUAGE_GR;

        $this->setOption("langid", $language);

        return $this;
    }

    public function getLanguage()
    {
        return $this->getOption("langid");
    }

    private function setOption($key, $value)
    {
        $key   = trim($key);
        $value = is_string($value) ? trim($value) : $value;

        $this->options[$key] = $value;

        return $this;
    }

    private function getOption($key)
    {
        return
            array_key_exists($key, $this->options)
                ? $this->options[$key]
                : null;
    }

    public function getByMonth($year = null, $month = null)
    {
        if (is_null($year)) $year = date("Y");
        if (is_null($month)) $month = date("j");

        $options =
            [
                "cyear"  => (int) $year,
                "cmonth" => (int) $month,
            ];

        return $this->execute("getbymonth.php", $options);
    }

    private function execute($script, array $options)
    {
        $url      = $this->getRequestUrl($script, $options);
        $response = $this->fetchResponse($url);

        if (isset($response->error)) {
            if ($response->error == "101") throw new \RuntimeException("Invalid username.");
            if ($response->error == "102") throw new \RuntimeException("Invalid password.");
            if ($response->error == "103") throw new \RuntimeException("Invalid search criteria.");
            if ($response->error == "104") return false;
        }

        return $response;
    }

    private function fetchResponse($url)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_USERAGENT, self::USER_AGENT . "/" . self::VERSION);

        $response = curl_exec($curl);
        if ($response === false) throw new \RuntimeException(curl_error($curl));

        curl_close($curl);

        $response = @simplexml_load_string($response);
        if (!$response) throw new \RuntimeException("Couldn't parse response.");

        return $response;
    }

    private function getRequestUrl($script, array $options)
    {
        $url     = self::ENDPOINT . urlencode($script) . "?";
        $options = array_merge($this->options, $options);

        foreach ($options as $key => $value) {
            if (empty($value)) continue;

            $url .= urlencode($key) . "=" . urlencode($value) . "&";
        }

        $url = rtrim($url, "&");

        return $url;
    }


}