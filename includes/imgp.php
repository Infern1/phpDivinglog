<?php

/**
 * Resize and crop images on the fly, store generated images in a cache.
 *
 * This version is a all-in-one version of img.php, it is not dependant an any other file
 * so you can simply copy it to any place you want it.
 *
 * @author  Mikael Roos mos@dbwebb.se
 * @example http://dbwebb.se/opensource/cimage
 * @link    https://github.com/mosbth/cimage
 *
 */
define("CIMAGE_BUNDLE", true);


/**
 * Change configuration details in the array below or create a separate file
 * where you store the configuration details.
 *
 * The configuration file should be named the same name as this file and then
 * add '_config.php'. If this file is named 'img.php' then name the
 * config file should be named 'img_config.php'.
 *
 * The settings below are only a few of the available ones. Check the file in
 * webroot/img_config.php for a complete list of configuration options.
 */
$config = array(

    'mode'         => 'production',               // 'production', 'development', 'strict'
    'image_path'   =>  __DIR__ . '/../',
    'cache_path'   =>  __DIR__ . '/../compile/',
    //'alias_path'   =>  __DIR__ . '/img/alias/',
    //'remote_allow' => true,
    //'password'     => false,                      // "secret-password",

);



// Version of cimage and img.php
define("CIMAGE_VERSION", "v0.8.1 (2020-06-08)");

// For CRemoteImage
define("CIMAGE_USER_AGENT", "CImage/" . CIMAGE_VERSION);

// Image type IMG_WEBP is only defined from 5.6.25
if (!defined("IMG_WEBP")) {
    define("IMG_WEBP", -1);
}



/**
 * General functions to use in img.php.
 */



/**
 * Trace and log execution to logfile, useful for debugging and development.
 *
 * @param string $msg message to log to file.
 *
 * @return void
 */
function trace($msg)
{
    $file = CIMAGE_DEBUG_FILE;
    if (!is_writable($file)) {
        return;
    }

    $timer = number_format((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 6);
    $details  = "{$timer}ms";
    $details .= ":" . round(memory_get_peak_usage() / 1024 / 1024, 3) . "MB";
    $details .= ":" . count(get_included_files());
    file_put_contents($file, "$details:$msg\n", FILE_APPEND);
}



/**
 * Display error message.
 *
 * @param string $msg to display.
 * @param int $type of HTTP error to display.
 *
 * @return void
 */
function errorPage($msg, $type = 500)
{
    global $mode;

    switch ($type) {
        case 403:
            $header = "403 Forbidden";
            break;
        case 404:
            $header = "404 Not Found";
            break;
        default:
            $header = "500 Internal Server Error";
    }

    if ($mode == "strict") {
        $header = "404 Not Found";
    }

    header("HTTP/1.0 $header");

    if ($mode == "development") {
        die("[img.php] $msg");
    }

    error_log("[img.php] $msg");
    die("HTTP/1.0 $header");
}



/**
 * Get input from query string or return default value if not set.
 *
 * @param mixed $key     as string or array of string values to look for in $_GET.
 * @param mixed $default value to return when $key is not set in $_GET.
 *
 * @return mixed value from $_GET or default value.
 */
function get($key, $default = null)
{
    if (is_array($key)) {
        foreach ($key as $val) {
            if (isset($_GET[$val])) {
                return $_GET[$val];
            }
        }
    } elseif (isset($_GET[$key])) {
        return $_GET[$key];
    }
    return $default;
}



/**
 * Get input from query string and set to $defined if defined or else $undefined.
 *
 * @param mixed $key       as string or array of string values to look for in $_GET.
 * @param mixed $defined   value to return when $key is set in $_GET.
 * @param mixed $undefined value to return when $key is not set in $_GET.
 *
 * @return mixed value as $defined or $undefined.
 */
function getDefined($key, $defined, $undefined)
{
    return get($key) === null ? $undefined : $defined;
}



/**
 * Get value of input from query string or else $undefined.
 *
 * @param mixed $key       as string or array of string values to look for in $_GET.
 * @param mixed $undefined value to return when $key has no, or empty value in $_GET.
 *
 * @return mixed value as or $undefined.
 */
function getValue($key, $undefined)
{
    $val = get($key);
    if (is_null($val) || $val === "") {
        return $undefined;
    }
    return $val;
}



/**
 * Get value from config array or default if key is not set in config array.
 *
 * @param string $key    the key in the config array.
 * @param mixed $default value to be default if $key is not set in config.
 *
 * @return mixed value as $config[$key] or $default.
 */
function getConfig($key, $default)
{
    global $config;
    return isset($config[$key])
        ? $config[$key]
        : $default;
}



/**
 * Log when verbose mode, when used without argument it returns the result.
 *
 * @param string $msg to log.
 *
 * @return void or array.
 */
function verbose($msg = null, $arg = "")
{
    global $verbose, $verboseFile;
    static $log = array();

    if (!($verbose || $verboseFile)) {
        return;
    }

    if (is_null($msg)) {
        return $log;
    }

    if (is_null($arg)) {
        $arg = "null";
    } elseif ($arg === false) {
        $arg = "false";
    } elseif ($arg === true) {
        $arg = "true";
    }

    $log[] = $msg . $arg;
}



/**
 * Log when verbose mode, when used without argument it returns the result.
 *
 * @param string $msg to log.
 *
 * @return void or array.
 */
function checkExternalCommand($what, $enabled, $commandString)
{
    $no = $enabled ? null : 'NOT';
    $text = "Post processing $what is $no enabled.<br>";

    list($command) = explode(" ", $commandString);
    $no = is_executable($command) ? null : 'NOT';
    $text .= "The command for $what is $no an executable.<br>";

    return $text;
}



/**
 * Get a image from a remote server using HTTP GET and If-Modified-Since.
 *
 */
class CHttpGet
{
    private $request  = array();
    private $response = array();



    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->request['header'] = array();
    }



    /**
     * Build an encoded url.
     *
     * @param string $baseUrl This is the original url which will be merged.
     * @param string $merge   Thse parts should be merged into the baseUrl,
     *                        the format is as parse_url.
     *
     * @return string $url as the modified url.
     */
    public function buildUrl($baseUrl, $merge)
    {
        $parts = parse_url($baseUrl);
        $parts = array_merge($parts, $merge);

        $url  = $parts['scheme'];
        $url .= "://";
        $url .= $parts['host'];
        $url .= isset($parts['port'])
            ? ":" . $parts['port']
            : "";
        $url .= $parts['path'];

        return $url;
    }



    /**
     * Set the url for the request.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $parts = parse_url($url);

        $path = "";
        if (isset($parts['path'])) {
            $pathParts = explode('/', $parts['path']);
            unset($pathParts[0]);
            foreach ($pathParts as $value) {
                $path .= "/" . rawurlencode($value);
            }
        }
        $url = $this->buildUrl($url, array("path" => $path));

        $this->request['url'] = $url;
        return $this;
    }



    /**
     * Set custom header field for the request.
     *
     * @param string $field
     * @param string $value
     *
     * @return $this
     */
    public function setHeader($field, $value)
    {
        $this->request['header'][] = "$field: $value";
        return $this;
    }



    /**
     * Set header fields for the request.
     *
     * @param string $field
     * @param string $value
     *
     * @return $this
     */
    public function parseHeader()
    {
        //$header = explode("\r\n", rtrim($this->response['headerRaw'], "\r\n"));

        $rawHeaders = rtrim($this->response['headerRaw'], "\r\n");
        # Handle multiple responses e.g. with redirections (proxies too)
        $headerGroups = explode("\r\n\r\n", $rawHeaders);
        # We're only interested in the last one
        $header = explode("\r\n", end($headerGroups));

        $output = array();

        if ('HTTP' === substr($header[0], 0, 4)) {
            list($output['version'], $output['status']) = explode(' ', $header[0]);
            unset($header[0]);
        }

        foreach ($header as $entry) {
            $pos = strpos($entry, ':');
            $output[trim(substr($entry, 0, $pos))] = trim(substr($entry, $pos + 1));
        }

        $this->response['header'] = $output;
        return $this;
    }



    /**
     * Perform the request.
     *
     * @param boolean $debug set to true to dump headers.
     *
     * @throws Exception when curl fails to retrieve url.
     *
     * @return boolean
     */
    public function doGet($debug = false)
    {
        $options = array(
            CURLOPT_URL             => $this->request['url'],
            CURLOPT_HEADER          => 1,
            CURLOPT_HTTPHEADER      => $this->request['header'],
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLINFO_HEADER_OUT     => $debug,
            CURLOPT_CONNECTTIMEOUT  => 5,
            CURLOPT_TIMEOUT         => 5,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_MAXREDIRS       => 2,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if (!$response) {
            throw new Exception("Failed retrieving url, details follows: " . curl_error($ch));
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->response['headerRaw'] = substr($response, 0, $headerSize);
        $this->response['body']      = substr($response, $headerSize);

        $this->parseHeader();

        if ($debug) {
            $info = curl_getinfo($ch);
            echo "Request header<br><pre>", var_dump($info['request_header']), "</pre>";
            echo "Response header (raw)<br><pre>", var_dump($this->response['headerRaw']), "</pre>";
            echo "Response header (parsed)<br><pre>", var_dump($this->response['header']), "</pre>";
        }

        curl_close($ch);
        return true;
    }



    /**
     * Get HTTP code of response.
     *
     * @return integer as HTTP status code or null if not available.
     */
    public function getStatus()
    {
        return isset($this->response['header']['status'])
            ? (int) $this->response['header']['status']
            : null;
    }



    /**
     * Get file modification time of response.
     *
     * @return int as timestamp.
     */
    public function getLastModified()
    {
        return isset($this->response['header']['Last-Modified'])
            ? strtotime($this->response['header']['Last-Modified'])
            : null;
    }



    /**
     * Get content type.
     *
     * @return string as the content type or null if not existing or invalid.
     */
    public function getContentType()
    {
        $type = isset($this->response['header']['Content-Type'])
            ? $this->response['header']['Content-Type']
            : null;

        return preg_match('#[a-z]+/[a-z]+#', $type)
            ? $type
            : null;
    }



    /**
     * Get file modification time of response.
     *
     * @param mixed $default as default value (int seconds) if date is
     *                       missing in response header.
     *
     * @return int as timestamp or $default if Date is missing in
     *             response header.
     */
    public function getDate($default = false)
    {
        return isset($this->response['header']['Date'])
            ? strtotime($this->response['header']['Date'])
            : $default;
    }



    /**
     * Get max age of cachable item.
     *
     * @param mixed $default as default value if date is missing in response
     *                       header.
     *
     * @return int as timestamp or false if not available.
     */
    public function getMaxAge($default = false)
    {
        $cacheControl = isset($this->response['header']['Cache-Control'])
            ? $this->response['header']['Cache-Control']
            : null;

        $maxAge = null;
        if ($cacheControl) {
            // max-age=2592000
            $part = explode('=', $cacheControl);
            $maxAge = ($part[0] == "max-age")
                ? (int) $part[1]
                : null;
        }

        if ($maxAge) {
            return $maxAge;
        }

        $expire = isset($this->response['header']['Expires'])
            ? strtotime($this->response['header']['Expires'])
            : null;

        return $expire ? $expire : $default;
    }



    /**
     * Get body of response.
     *
     * @return string as body.
     */
    public function getBody()
    {
        return $this->response['body'];
    }
}



/**
 * Get a image from a remote server using HTTP GET and If-Modified-Since.
 *
 */
class CRemoteImage
{
    /**
     * Path to cache files.
     */
    private $saveFolder = null;



    /**
     * Use cache or not.
     */
    private $useCache = true;



    /**
     * HTTP object to aid in download file.
     */
    private $http;



    /**
     * Status of the HTTP request.
     */
    private $status;



    /**
     * Defalt age for cached items 60*60*24*7.
     */
    private $defaultMaxAge = 604800;



    /**
     * Url of downloaded item.
     */
    private $url;



    /**
     * Base name of cache file for downloaded item and name of image.
     */
    private $fileName;



    /**
     * Filename for json-file with details of cached item.
     */
    private $fileJson;



    /**
     * Cache details loaded from file.
     */
    private $cache;



    /**
     * Get status of last HTTP request.
     *
     * @return int as status
     */
    public function getStatus()
    {
        return $this->status;
    }



    /**
     * Get JSON details for cache item.
     *
     * @return array with json details on cache.
     */
    public function getDetails()
    {
        return $this->cache;
    }



    /**
     * Set the path to the cache directory.
     *
     * @param boolean $use true to use the cache and false to ignore cache.
     *
     * @return $this
     */
    public function setCache($path)
    {
        $this->saveFolder = rtrim($path, "/") . "/";
        return $this;
    }



    /**
     * Check if cache is writable or throw exception.
     *
     * @return $this
     *
     * @throws Exception if cahce folder is not writable.
     */
    public function isCacheWritable()
    {
        if (!is_writable($this->saveFolder)) {
            throw new Exception("Cache folder is not writable for downloaded files.");
        }
        return $this;
    }



    /**
     * Decide if the cache should be used or not before trying to download
     * a remote file.
     *
     * @param boolean $use true to use the cache and false to ignore cache.
     *
     * @return $this
     */
    public function useCache($use = true)
    {
        $this->useCache = $use;
        return $this;
    }



    /**
     * Set header fields.
     *
     * @return $this
     */
    public function setHeaderFields()
    {
        $cimageVersion = "CImage";
        if (defined("CIMAGE_USER_AGENT")) {
            $cimageVersion = CIMAGE_USER_AGENT;
        }

        $this->http->setHeader("User-Agent", "$cimageVersion (PHP/" . phpversion() . " cURL)");
        $this->http->setHeader("Accept", "image/jpeg,image/png,image/gif");

        if ($this->useCache) {
            $this->http->setHeader("Cache-Control", "max-age=0");
        } else {
            $this->http->setHeader("Cache-Control", "no-cache");
            $this->http->setHeader("Pragma", "no-cache");
        }
    }



    /**
     * Save downloaded resource to cache.
     *
     * @return string as path to saved file or false if not saved.
     */
    public function save()
    {
        $this->cache = array();
        $date         = $this->http->getDate(time());
        $maxAge       = $this->http->getMaxAge($this->defaultMaxAge);
        $lastModified = $this->http->getLastModified();
        $type         = $this->http->getContentType();

        $this->cache['Date']           = gmdate("D, d M Y H:i:s T", $date);
        $this->cache['Max-Age']        = $maxAge;
        $this->cache['Content-Type']   = $type;
        $this->cache['Url']            = $this->url;

        if ($lastModified) {
            $this->cache['Last-Modified'] = gmdate("D, d M Y H:i:s T", $lastModified);
        }

        // Save only if body is a valid image
        $body = $this->http->getBody();
        $img = imagecreatefromstring($body);

        if ($img !== false) {
            file_put_contents($this->fileName, $body);
            file_put_contents($this->fileJson, json_encode($this->cache));
            return $this->fileName;
        }

        return false;
    }



    /**
     * Got a 304 and updates cache with new age.
     *
     * @return string as path to cached file.
     */
    public function updateCacheDetails()
    {
        $date         = $this->http->getDate(time());
        $maxAge       = $this->http->getMaxAge($this->defaultMaxAge);
        $lastModified = $this->http->getLastModified();

        $this->cache['Date']    = gmdate("D, d M Y H:i:s T", $date);
        $this->cache['Max-Age'] = $maxAge;

        if ($lastModified) {
            $this->cache['Last-Modified'] = gmdate("D, d M Y H:i:s T", $lastModified);
        }

        file_put_contents($this->fileJson, json_encode($this->cache));
        return $this->fileName;
    }



    /**
     * Download a remote file and keep a cache of downloaded files.
     *
     * @param string $url a remote url.
     *
     * @throws Exception when status code does not match 200 or 304.
     *
     * @return string as path to downloaded file or false if failed.
     */
    public function download($url)
    {
        $this->http = new CHttpGet();
        $this->url = $url;

        // First check if the cache is valid and can be used
        $this->loadCacheDetails();

        if ($this->useCache) {
            $src = $this->getCachedSource();
            if ($src) {
                $this->status = 1;
                return $src;
            }
        }

        // Do a HTTP request to download item
        $this->setHeaderFields();
        $this->http->setUrl($this->url);
        $this->http->doGet();

        $this->status = $this->http->getStatus();
        if ($this->status === 200) {
            $this->isCacheWritable();
            return $this->save();
        } elseif ($this->status === 304) {
            $this->isCacheWritable();
            return $this->updateCacheDetails();
        }

        throw new Exception("Unknown statuscode when downloading remote image: " . $this->status);
    }



    /**
     * Get the path to the cached image file if the cache is valid.
     *
     * @return $this
     */
    public function loadCacheDetails()
    {
        $cacheFile = md5($this->url);
        $this->fileName = $this->saveFolder . $cacheFile;
        $this->fileJson = $this->fileName . ".json";
        if (is_readable($this->fileJson)) {
            $this->cache = json_decode(file_get_contents($this->fileJson), true);
        }
    }



    /**
     * Get the path to the cached image file if the cache is valid.
     *
     * @return string as the path ot the image file or false if no cache.
     */
    public function getCachedSource()
    {
        $imageExists = is_readable($this->fileName);

        // Is cache valid?
        $date   = strtotime($this->cache['Date']);
        $maxAge = $this->cache['Max-Age'];
        $now    = time();

        if ($imageExists && $date + $maxAge > $now) {
            return $this->fileName;
        }

        // Prepare for a 304 if available
        if ($imageExists && isset($this->cache['Last-Modified'])) {
            $this->http->setHeader("If-Modified-Since", $this->cache['Last-Modified']);
        }

        return false;
    }
}



/**
 * Act as whitelist (or blacklist).
 *
 */
class CWhitelist
{
    /**
     * Array to contain the whitelist options.
     */
    private $whitelist = array();



    /**
     * Set the whitelist from an array of strings, each item in the
     * whitelist should be a regexp without the surrounding / or #.
     *
     * @param array $whitelist with all valid options,
     *                         default is to clear the whitelist.
     *
     * @return $this
     */
    public function set($whitelist = array())
    {
        if (!is_array($whitelist)) {
            throw new Exception("Whitelist is not of a supported format.");
        }

        $this->whitelist = $whitelist;
        return $this;
    }



    /**
     * Check if item exists in the whitelist.
     *
     * @param string $item      string to check.
     * @param array  $whitelist optional with all valid options, default is null.
     *
     * @return boolean true if item is in whitelist, else false.
     */
    public function check($item, $whitelist = null)
    {
        if ($whitelist !== null) {
            $this->set($whitelist);
        }

        if (empty($item) or empty($this->whitelist)) {
            return false;
        }

        foreach ($this->whitelist as $regexp) {
            if (preg_match("#$regexp#", $item)) {
                return true;
            }
        }

        return false;
    }
}



/**
 * Create an ASCII version of an image.
 *
 */
class CAsciiArt
{
    /**
     * Character set to use.
     */
    private $characterSet = array(
        'one' => "#0XT|:,.' ",
        'two' => "@%#*+=-:. ",
        'three' => "$@B%8&WM#*oahkbdpqwmZO0QLCJUYXzcvunxrjft/\|()1{}[]?-_+~<>i!lI;:,\"^`'. "
    );



    /**
     * Current character set.
     */
    private $characters = null;



    /**
     * Length of current character set.
     */
    private $charCount = null;



    /**
     * Scale of the area to swap to a character.
     */
    private $scale = null;



    /**
     * Strategy to calculate luminance.
     */
    private $luminanceStrategy = null;



    /**
     * Constructor which sets default options.
     */
    public function __construct()
    {
        $this->setOptions();
    }



    /**
     * Add a custom character set.
     *
     * @param string $key   for the character set.
     * @param string $value for the character set.
     *
     * @return $this
     */
    public function addCharacterSet($key, $value)
    {
        $this->characterSet[$key] = $value;
        return $this;
    }



    /**
     * Set options for processing, defaults are available.
     *
     * @param array $options to use as default settings.
     *
     * @return $this
     */
    public function setOptions($options = array())
    {
        $default = array(
            "characterSet" => 'two',
            "scale" => 14,
            "luminanceStrategy" => 3,
            "customCharacterSet" => null,
        );
        $default = array_merge($default, $options);

        if (!is_null($default['customCharacterSet'])) {
            $this->addCharacterSet('custom', $default['customCharacterSet']);
            $default['characterSet'] = 'custom';
        }

        $this->scale = $default['scale'];
        $this->characters = $this->characterSet[$default['characterSet']];
        $this->charCount = strlen($this->characters);
        $this->luminanceStrategy = $default['luminanceStrategy'];

        return $this;
    }



    /**
     * Create an Ascii image from an image file.
     *
     * @param string $filename of the image to use.
     *
     * @return string $ascii with the ASCII image.
     */
    public function createFromFile($filename)
    {
        $img = imagecreatefromstring(file_get_contents($filename));
        list($width, $height) = getimagesize($filename);

        $ascii = null;
        $incY = $this->scale;
        $incX = $this->scale / 2;

        for ($y = 0; $y < $height - 1; $y += $incY) {
            for ($x = 0; $x < $width - 1; $x += $incX) {
                $toX = min($x + $this->scale / 2, $width - 1);
                $toY = min($y + $this->scale, $height - 1);
                $luminance = $this->luminanceAreaAverage($img, $x, $y, $toX, $toY);
                $ascii .= $this->luminance2character($luminance);
            }
            $ascii .= PHP_EOL;
        }

        return $ascii;
    }



    /**
     * Get the luminance from a region of an image using average color value.
     *
     * @param string  $img the image.
     * @param integer $x1  the area to get pixels from.
     * @param integer $y1  the area to get pixels from.
     * @param integer $x2  the area to get pixels from.
     * @param integer $y2  the area to get pixels from.
     *
     * @return integer $luminance with a value between 0 and 100.
     */
    public function luminanceAreaAverage($img, $x1, $y1, $x2, $y2)
    {
        $numPixels = ($x2 - $x1 + 1) * ($y2 - $y1 + 1);
        $luminance = 0;

        for ($x = $x1; $x <= $x2; $x++) {
            for ($y = $y1; $y <= $y2; $y++) {
                $rgb   = imagecolorat($img, $x, $y);
                $red   = (($rgb >> 16) & 0xFF);
                $green = (($rgb >> 8) & 0xFF);
                $blue  = ($rgb & 0xFF);
                $luminance += $this->getLuminance($red, $green, $blue);
            }
        }

        return $luminance / $numPixels;
    }



    /**
     * Calculate luminance value with different strategies.
     *
     * @param integer $red   The color red.
     * @param integer $green The color green.
     * @param integer $blue  The color blue.
     *
     * @return float $luminance with a value between 0 and 1.
     */
    public function getLuminance($red, $green, $blue)
    {
        switch ($this->luminanceStrategy) {
            case 1:
                $luminance = ($red * 0.2126 + $green * 0.7152 + $blue * 0.0722) / 255;
                break;
            case 2:
                $luminance = ($red * 0.299 + $green * 0.587 + $blue * 0.114) / 255;
                break;
            case 3:
                $luminance = sqrt(0.299 * pow($red, 2) + 0.587 * pow($green, 2) + 0.114 * pow($blue, 2)) / 255;
                break;
            case 0:
            default:
                $luminance = ($red + $green + $blue) / (255 * 3);
        }

        return $luminance;
    }



    /**
     * Translate the luminance value to a character.
     *
     * @param string $position a value between 0-100 representing the
     *                         luminance.
     *
     * @return string with the ascii character.
     */
    public function luminance2character($luminance)
    {
        $position = (int) round($luminance * ($this->charCount - 1));
        $char = $this->characters[$position];
        return $char;
    }
}



/**
 * Resize and crop images on the fly, store generated images in a cache.
 *
 * @author  Mikael Roos mos@dbwebb.se
 * @example http://dbwebb.se/opensource/cimage
 * @link    https://github.com/mosbth/cimage
 */
class CImage
{

    /**
     * Constants type of PNG image
     */
    const PNG_GREYSCALE         = 0;
    const PNG_RGB               = 2;
    const PNG_RGB_PALETTE       = 3;
    const PNG_GREYSCALE_ALPHA   = 4;
    const PNG_RGB_ALPHA         = 6;



    /**
     * Constant for default image quality when not set
     */
    const JPEG_QUALITY_DEFAULT = 60;



    /**
     * Quality level for JPEG images.
     */
    private $quality;



    /**
     * Is the quality level set from external use (true) or is it default (false)?
     */
    private $useQuality = false;



    /**
     * Constant for default image quality when not set
     */
    const PNG_COMPRESSION_DEFAULT = -1;



    /**
     * Compression level for PNG images.
     */
    private $compress;



    /**
     * Is the compress level set from external use (true) or is it default (false)?
     */
    private $useCompress = false;




    /**
     * Add HTTP headers for outputing image.
     */
    private $HTTPHeader = array();



    /**
     * Default background color, red, green, blue, alpha.
     *
     * @todo remake when upgrading to PHP 5.5
     */
    /*
    const BACKGROUND_COLOR = array(
        'red'   => 0,
        'green' => 0,
        'blue'  => 0,
        'alpha' => null,
    );*/



    /**
     * Default background color to use.
     *
     * @todo remake when upgrading to PHP 5.5
     */
    //private $bgColorDefault = self::BACKGROUND_COLOR;
    private $bgColorDefault = array(
        'red'   => 0,
        'green' => 0,
        'blue'  => 0,
        'alpha' => null,
    );


    /**
     * Background color to use, specified as part of options.
     */
    private $bgColor;



    /**
     * Where to save the target file.
     */
    private $saveFolder;



    /**
     * The working image object.
     */
    private $image;



    /**
     * Image filename, may include subdirectory, relative from $imageFolder
     */
    private $imageSrc;



    /**
     * Actual path to the image, $imageFolder . '/' . $imageSrc
     */
    private $pathToImage;



    /**
     * File type for source image, as provided by getimagesize()
     */
    private $fileType;



    /**
     * File extension to use when saving image.
     */
    private $extension;



    /**
     * Output format, supports null (image) or json.
     */
    private $outputFormat = null;



    /**
     * Do lossy output using external postprocessing tools.
     */
    private $lossy = null;



    /**
     * Verbose mode to print out a trace and display the created image
     */
    private $verbose = false;



    /**
     * Keep a log/trace on what happens
     */
    private $log = array();



    /**
     * Handle image as palette image
     */
    private $palette;



    /**
     * Target filename, with path, to save resulting image in.
     */
    private $cacheFileName;



    /**
     * Set a format to save image as, or null to use original format.
     */
    private $saveAs;


    /**
     * Path to command for lossy optimize, for example pngquant.
     */
    private $pngLossy;
    private $pngLossyCmd;



    /**
     * Path to command for filter optimize, for example optipng.
     */
    private $pngFilter;
    private $pngFilterCmd;



    /**
     * Path to command for deflate optimize, for example pngout.
     */
    private $pngDeflate;
    private $pngDeflateCmd;



    /**
     * Path to command to optimize jpeg images, for example jpegtran or null.
     */
    private $jpegOptimize;
    private $jpegOptimizeCmd;



    /**
     * Image dimensions, calculated from loaded image.
     */
    private $width;  // Calculated from source image
    private $height; // Calculated from source image


    /**
     * New image dimensions, incoming as argument or calculated.
     */
    private $newWidth;
    private $newWidthOrig;  // Save original value
    private $newHeight;
    private $newHeightOrig; // Save original value


    /**
     * Change target height & width when different dpr, dpr 2 means double image dimensions.
     */
    private $dpr = 1;


    /**
     * Always upscale images, even if they are smaller than target image.
     */
    const UPSCALE_DEFAULT = true;
    private $upscale = self::UPSCALE_DEFAULT;



    /**
     * Array with details on how to crop, incoming as argument and calculated.
     */
    public $crop;
    public $cropOrig; // Save original value


    /**
     * String with details on how to do image convolution. String
     * should map a key in the $convolvs array or be a string of
     * 11 float values separated by comma. The first nine builds
     * up the matrix, then divisor and last offset.
     */
    private $convolve;


    /**
     * Custom convolution expressions, matrix 3x3, divisor and offset.
     */
    private $convolves = array(
        'lighten'       => '0,0,0, 0,12,0, 0,0,0, 9, 0',
        'darken'        => '0,0,0, 0,6,0, 0,0,0, 9, 0',
        'sharpen'       => '-1,-1,-1, -1,16,-1, -1,-1,-1, 8, 0',
        'sharpen-alt'   => '0,-1,0, -1,5,-1, 0,-1,0, 1, 0',
        'emboss'        => '1,1,-1, 1,3,-1, 1,-1,-1, 3, 0',
        'emboss-alt'    => '-2,-1,0, -1,1,1, 0,1,2, 1, 0',
        'blur'          => '1,1,1, 1,15,1, 1,1,1, 23, 0',
        'gblur'         => '1,2,1, 2,4,2, 1,2,1, 16, 0',
        'edge'          => '-1,-1,-1, -1,8,-1, -1,-1,-1, 9, 0',
        'edge-alt'      => '0,1,0, 1,-4,1, 0,1,0, 1, 0',
        'draw'          => '0,-1,0, -1,5,-1, 0,-1,0, 0, 0',
        'mean'          => '1,1,1, 1,1,1, 1,1,1, 9, 0',
        'motion'        => '1,0,0, 0,1,0, 0,0,1, 3, 0',
    );


    /**
     * Resize strategy to fill extra area with background color.
     * True or false.
     */
    private $fillToFit;



    /**
     * To store value for option scale.
     */
    private $scale;



    /**
     * To store value for option.
     */
    private $rotateBefore;



    /**
     * To store value for option.
     */
    private $rotateAfter;



    /**
     * To store value for option.
     */
    private $autoRotate;



    /**
     * To store value for option.
     */
    private $sharpen;



    /**
     * To store value for option.
     */
    private $emboss;



    /**
     * To store value for option.
     */
    private $blur;



    /**
     * Used with option area to set which parts of the image to use.
     */
    private $offset;



    /**
     * Calculate target dimension for image when using fill-to-fit resize strategy.
     */
    private $fillWidth;
    private $fillHeight;



    /**
     * Allow remote file download, default is to disallow remote file download.
     */
    private $allowRemote = false;



    /**
     * Path to cache for remote download.
     */
    private $remoteCache;



    /**
     * Pattern to recognize a remote file.
     */
    //private $remotePattern = '#^[http|https]://#';
    private $remotePattern = '#^https?://#';



    /**
     * Use the cache if true, set to false to ignore the cached file.
     */
    private $useCache = true;


    /**
     * Disable the fasttrackCacke to start with, inject an object to enable it.
     */
    private $fastTrackCache = null;



    /*
     * Set whitelist for valid hostnames from where remote source can be
     * downloaded.
     */
    private $remoteHostWhitelist = null;



    /*
     * Do verbose logging to file by setting this to a filename.
     */
    private $verboseFileName = null;



    /*
     * Output to ascii can take som options as an array.
     */
    private $asciiOptions = array();



    /*
     * Use interlaced progressive mode for JPEG images.
     */
    private $interlace = false;



    /*
     * Image copy strategy, defaults to RESAMPLE.
     */
    const RESIZE = 1;
    const RESAMPLE = 2;
    private $copyStrategy = NULL;



    /**
     * Properties, the class is mutable and the method setOptions()
     * decides (partly) what properties are created.
     *
     * @todo Clean up these and check if and how they are used
     */

    public $keepRatio;
    public $cropToFit;
    private $cropWidth;
    private $cropHeight;
    public $crop_x;
    public $crop_y;
    public $filters;
    private $attr; // Calculated from source image




    /**
     * Constructor, can take arguments to init the object.
     *
     * @param string $imageSrc    filename which may contain subdirectory.
     * @param string $imageFolder path to root folder for images.
     * @param string $saveFolder  path to folder where to save the new file or null to skip saving.
     * @param string $saveName    name of target file when saveing.
     */
    public function __construct($imageSrc = null, $imageFolder = null, $saveFolder = null, $saveName = null)
    {
        $this->setSource($imageSrc, $imageFolder);
        $this->setTarget($saveFolder, $saveName);
    }



    /**
     * Inject object and use it, must be available as member.
     *
     * @param string $property to set as object.
     * @param object $object   to set to property.
     *
     * @return $this
     */
    public function injectDependency($property, $object)
    {
        if (!property_exists($this, $property)) {
            $this->raiseError("Injecting unknown property.");
        }
        $this->$property = $object;
        return $this;
    }



    /**
     * Set verbose mode.
     *
     * @param boolean $mode true or false to enable and disable verbose mode,
     *                      default is true.
     *
     * @return $this
     */
    public function setVerbose($mode = true)
    {
        $this->verbose = $mode;
        return $this;
    }



    /**
     * Set save folder, base folder for saving cache files.
     *
     * @todo clean up how $this->saveFolder is used in other methods.
     *
     * @param string $path where to store cached files.
     *
     * @return $this
     */
    public function setSaveFolder($path)
    {
        $this->saveFolder = $path;
        return $this;
    }



    /**
     * Use cache or not.
     *
     * @param boolean $use true or false to use cache.
     *
     * @return $this
     */
    public function useCache($use = true)
    {
        $this->useCache = $use;
        return $this;
    }



    /**
     * Create and save a dummy image. Use dimensions as stated in
     * $this->newWidth, or $width or default to 100 (same for height.
     *
     * @param integer $width  use specified width for image dimension.
     * @param integer $height use specified width for image dimension.
     *
     * @return $this
     */
    public function createDummyImage($width = null, $height = null)
    {
        $this->newWidth  = $this->newWidth  ?: $width  ?: 100;
        $this->newHeight = $this->newHeight ?: $height ?: 100;

        $this->image = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);

        return $this;
    }



    /**
     * Allow or disallow remote image download.
     *
     * @param boolean $allow   true or false to enable and disable.
     * @param string  $cache   path to cache dir.
     * @param string  $pattern to use to detect if its a remote file.
     *
     * @return $this
     */
    public function setRemoteDownload($allow, $cache, $pattern = null)
    {
        $this->allowRemote = $allow;
        $this->remoteCache = $cache;
        $this->remotePattern = is_null($pattern) ? $this->remotePattern : $pattern;

        $this->log(
            "Set remote download to: "
                . ($this->allowRemote ? "true" : "false")
                . " using pattern "
                . $this->remotePattern
        );

        return $this;
    }



    /**
     * Check if the image resource is a remote file or not.
     *
     * @param string $src check if src is remote.
     *
     * @return boolean true if $src is a remote file, else false.
     */
    public function isRemoteSource($src)
    {
        $remote = preg_match($this->remotePattern, $src);
        $this->log("Detected remote image: " . ($remote ? "true" : "false"));
        return !!$remote;
    }



    /**
     * Set whitelist for valid hostnames from where remote source can be
     * downloaded.
     *
     * @param array $whitelist with regexp hostnames to allow download from.
     *
     * @return $this
     */
    public function setRemoteHostWhitelist($whitelist = null)
    {
        $this->remoteHostWhitelist = $whitelist;
        $this->log(
            "Setting remote host whitelist to: "
                . (is_null($whitelist) ? "null" : print_r($whitelist, 1))
        );
        return $this;
    }



    /**
     * Check if the hostname for the remote image, is on a whitelist,
     * if the whitelist is defined.
     *
     * @param string $src the remote source.
     *
     * @return boolean true if hostname on $src is in the whitelist, else false.
     */
    public function isRemoteSourceOnWhitelist($src)
    {
        if (is_null($this->remoteHostWhitelist)) {
            $this->log("Remote host on whitelist not configured - allowing.");
            return true;
        }

        $whitelist = new CWhitelist();
        $hostname = parse_url($src, PHP_URL_HOST);
        $allow = $whitelist->check($hostname, $this->remoteHostWhitelist);

        $this->log(
            "Remote host is on whitelist: "
                . ($allow ? "true" : "false")
        );
        return $allow;
    }



    /**
     * Check if file extension is valid as a file extension.
     *
     * @param string $extension of image file.
     *
     * @return $this
     */
    private function checkFileExtension($extension)
    {
        $valid = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        in_array(strtolower($extension), $valid)
            or $this->raiseError('Not a valid file extension.');

        return $this;
    }



    /**
     * Normalize the file extension.
     *
     * @param string $extension of image file or skip to use internal.
     *
     * @return string $extension as a normalized file extension.
     */
    private function normalizeFileExtension($extension = null)
    {
        $extension = strtolower($extension ? $extension : $this->extension);

        if ($extension == 'jpeg') {
            $extension = 'jpg';
        }

        return $extension;
    }



    /**
     * Download a remote image and return path to its local copy.
     *
     * @param string $src remote path to image.
     *
     * @return string as path to downloaded remote source.
     */
    public function downloadRemoteSource($src)
    {
        if (!$this->isRemoteSourceOnWhitelist($src)) {
            throw new Exception("Hostname is not on whitelist for remote sources.");
        }

        $remote = new CRemoteImage();

        if (!is_writable($this->remoteCache)) {
            $this->log("The remote cache is not writable.");
        }

        $remote->setCache($this->remoteCache);
        $remote->useCache($this->useCache);
        $src = $remote->download($src);

        $this->log("Remote HTTP status: " . $remote->getStatus());
        $this->log("Remote item is in local cache: $src");
        $this->log("Remote details on cache:" . print_r($remote->getDetails(), true));

        return $src;
    }



    /**
     * Set source file to use as image source.
     *
     * @param string $src of image.
     * @param string $dir as optional base directory where images are.
     *
     * @return $this
     */
    public function setSource($src, $dir = null)
    {
        if (!isset($src)) {
            $this->imageSrc = null;
            $this->pathToImage = null;
            return $this;
        }

        if ($this->allowRemote && $this->isRemoteSource($src)) {
            $src = $this->downloadRemoteSource($src);
            $dir = null;
        }

        if (!isset($dir)) {
            $dir = dirname($src);
            $src = basename($src);
        }

        $this->imageSrc     = ltrim($src, '/');
        $imageFolder        = rtrim($dir, '/');
        $this->pathToImage  = $imageFolder . '/' . $this->imageSrc;

        return $this;
    }



    /**
     * Set target file.
     *
     * @param string $src of target image.
     * @param string $dir as optional base directory where images are stored.
     *                    Uses $this->saveFolder if null.
     *
     * @return $this
     */
    public function setTarget($src = null, $dir = null)
    {
        if (!isset($src)) {
            $this->cacheFileName = null;
            return $this;
        }

        if (isset($dir)) {
            $this->saveFolder = rtrim($dir, '/');
        }

        $this->cacheFileName  = $this->saveFolder . '/' . $src;

        // Sanitize filename
        $this->cacheFileName = preg_replace('/^a-zA-Z0-9\.-_/', '', $this->cacheFileName);
        $this->log("The cache file name is: " . $this->cacheFileName);

        return $this;
    }



    /**
     * Get filename of target file.
     *
     * @return Boolean|String as filename of target or false if not set.
     */
    public function getTarget()
    {
        return $this->cacheFileName;
    }



    /**
     * Set options to use when processing image.
     *
     * @param array $args used when processing image.
     *
     * @return $this
     */
    public function setOptions($args)
    {
        $this->log("Set new options for processing image.");

        $defaults = array(
            // Options for calculate dimensions
            'newWidth'    => null,
            'newHeight'   => null,
            'aspectRatio' => null,
            'keepRatio'   => true,
            'cropToFit'   => false,
            'fillToFit'   => null,
            'crop'        => null, //array('width'=>null, 'height'=>null, 'start_x'=>0, 'start_y'=>0),
            'area'        => null, //'0,0,0,0',
            'upscale'     => self::UPSCALE_DEFAULT,

            // Options for caching or using original
            'useCache'    => true,
            'useOriginal' => true,

            // Pre-processing, before resizing is done
            'scale'        => null,
            'rotateBefore' => null,
            'autoRotate'  => false,

            // General options
            'bgColor'     => null,

            // Post-processing, after resizing is done
            'palette'     => null,
            'filters'     => null,
            'sharpen'     => null,
            'emboss'      => null,
            'blur'        => null,
            'convolve'       => null,
            'rotateAfter' => null,
            'interlace' => null,

            // Output format
            'outputFormat' => null,
            'dpr'          => 1,

            // Postprocessing using external tools
            'lossy' => null,
        );

        // Convert crop settings from string to array
        if (isset($args['crop']) && !is_array($args['crop'])) {
            $pices = explode(',', $args['crop']);
            $args['crop'] = array(
                'width'   => $pices[0],
                'height'  => $pices[1],
                'start_x' => $pices[2],
                'start_y' => $pices[3],
            );
        }

        // Convert area settings from string to array
        if (isset($args['area']) && !is_array($args['area'])) {
            $pices = explode(',', $args['area']);
            $args['area'] = array(
                'top'    => $pices[0],
                'right'  => $pices[1],
                'bottom' => $pices[2],
                'left'   => $pices[3],
            );
        }

        // Convert filter settings from array of string to array of array
        if (isset($args['filters']) && is_array($args['filters'])) {
            foreach ($args['filters'] as $key => $filterStr) {
                $parts = explode(',', $filterStr);
                $filter = $this->mapFilter($parts[0]);
                $filter['str'] = $filterStr;
                for ($i = 1; $i <= $filter['argc']; $i++) {
                    if (isset($parts[$i])) {
                        $filter["arg{$i}"] = $parts[$i];
                    } else {
                        throw new Exception(
                            'Missing arg to filter, review how many arguments are needed at
                            http://php.net/manual/en/function.imagefilter.php'
                        );
                    }
                }
                $args['filters'][$key] = $filter;
            }
        }

        // Merge default arguments with incoming and set properties.
        //$args = array_merge_recursive($defaults, $args);
        $args = array_merge($defaults, $args);
        foreach ($defaults as $key => $val) {
            $this->{$key} = $args[$key];
        }

        if ($this->bgColor) {
            $this->setDefaultBackgroundColor($this->bgColor);
        }

        // Save original values to enable re-calculating
        $this->newWidthOrig  = $this->newWidth;
        $this->newHeightOrig = $this->newHeight;
        $this->cropOrig      = $this->crop;

        return $this;
    }



    /**
     * Map filter name to PHP filter and id.
     *
     * @param string $name the name of the filter.
     *
     * @return array with filter settings
     * @throws Exception
     */
    private function mapFilter($name)
    {
        $map = array(
            'negate'          => array('id' => 0,  'argc' => 0, 'type' => IMG_FILTER_NEGATE),
            'grayscale'       => array('id' => 1,  'argc' => 0, 'type' => IMG_FILTER_GRAYSCALE),
            'brightness'      => array('id' => 2,  'argc' => 1, 'type' => IMG_FILTER_BRIGHTNESS),
            'contrast'        => array('id' => 3,  'argc' => 1, 'type' => IMG_FILTER_CONTRAST),
            'colorize'        => array('id' => 4,  'argc' => 4, 'type' => IMG_FILTER_COLORIZE),
            'edgedetect'      => array('id' => 5,  'argc' => 0, 'type' => IMG_FILTER_EDGEDETECT),
            'emboss'          => array('id' => 6,  'argc' => 0, 'type' => IMG_FILTER_EMBOSS),
            'gaussian_blur'   => array('id' => 7,  'argc' => 0, 'type' => IMG_FILTER_GAUSSIAN_BLUR),
            'selective_blur'  => array('id' => 8,  'argc' => 0, 'type' => IMG_FILTER_SELECTIVE_BLUR),
            'mean_removal'    => array('id' => 9,  'argc' => 0, 'type' => IMG_FILTER_MEAN_REMOVAL),
            'smooth'          => array('id' => 10, 'argc' => 1, 'type' => IMG_FILTER_SMOOTH),
            'pixelate'        => array('id' => 11, 'argc' => 2, 'type' => IMG_FILTER_PIXELATE),
        );

        if (isset($map[$name])) {
            return $map[$name];
        } else {
            throw new Exception('No such filter.');
        }
    }



    /**
     * Load image details from original image file.
     *
     * @param string $file the file to load or null to use $this->pathToImage.
     *
     * @return $this
     * @throws Exception
     */
    public function loadImageDetails($file = null)
    {
        $file = $file ? $file : $this->pathToImage;

        is_readable($file)
            or $this->raiseError('Image file does not exist.');

        $info = list($this->width, $this->height, $this->fileType) = getimagesize($file);
        if (empty($info)) {
            // To support webp
            $this->fileType = false;
            if (function_exists("exif_imagetype")) {
                $this->fileType = exif_imagetype($file);
                if ($this->fileType === false) {
                    if (function_exists("imagecreatefromwebp")) {
                        $webp = imagecreatefromwebp($file);
                        if ($webp !== false) {
                            $this->width  = imagesx($webp);
                            $this->height = imagesy($webp);
                            $this->fileType = IMG_WEBP;
                        }
                    }
                }
            }
        }

        if (!$this->fileType) {
            throw new Exception("Loading image details, the file doesn't seem to be a valid image.");
        }

        if ($this->verbose) {
            $this->log("Loading image details for: {$file}");
            $this->log(" Image width x height (type): {$this->width} x {$this->height} ({$this->fileType}).");
            $this->log(" Image filesize: " . filesize($file) . " bytes.");
            $this->log(" Image mimetype: " . $this->getMimeType());
        }

        return $this;
    }



    /**
     * Get mime type for image type.
     *
     * @return $this
     * @throws Exception
     */
    protected function getMimeType()
    {
        if ($this->fileType === IMG_WEBP) {
            return "image/webp";
        }

        return image_type_to_mime_type($this->fileType);
    }



    /**
     * Init new width and height and do some sanity checks on constraints, before any
     * processing can be done.
     *
     * @return $this
     * @throws Exception
     */
    public function initDimensions()
    {
        $this->log("Init dimension (before) newWidth x newHeight is {$this->newWidth} x {$this->newHeight}.");

        // width as %
        if (
            $this->newWidth
            && $this->newWidth[strlen($this->newWidth) - 1] == '%'
        ) {
            $this->newWidth = $this->width * substr($this->newWidth, 0, -1) / 100;
            $this->log("Setting new width based on % to {$this->newWidth}");
        }

        // height as %
        if (
            $this->newHeight
            && $this->newHeight[strlen($this->newHeight) - 1] == '%'
        ) {
            $this->newHeight = $this->height * substr($this->newHeight, 0, -1) / 100;
            $this->log("Setting new height based on % to {$this->newHeight}");
        }

        is_null($this->aspectRatio) or is_numeric($this->aspectRatio) or $this->raiseError('Aspect ratio out of range');

        // width & height from aspect ratio
        if ($this->aspectRatio && is_null($this->newWidth) && is_null($this->newHeight)) {
            if ($this->aspectRatio >= 1) {
                $this->newWidth   = $this->width;
                $this->newHeight  = $this->width / $this->aspectRatio;
                $this->log("Setting new width & height based on width & aspect ratio (>=1) to (w x h) {$this->newWidth} x {$this->newHeight}");
            } else {
                $this->newHeight  = $this->height;
                $this->newWidth   = $this->height * $this->aspectRatio;
                $this->log("Setting new width & height based on width & aspect ratio (<1) to (w x h) {$this->newWidth} x {$this->newHeight}");
            }
        } elseif ($this->aspectRatio && is_null($this->newWidth)) {
            $this->newWidth   = $this->newHeight * $this->aspectRatio;
            $this->log("Setting new width based on aspect ratio to {$this->newWidth}");
        } elseif ($this->aspectRatio && is_null($this->newHeight)) {
            $this->newHeight  = $this->newWidth / $this->aspectRatio;
            $this->log("Setting new height based on aspect ratio to {$this->newHeight}");
        }

        // Change width & height based on dpr
        if ($this->dpr != 1) {
            if (!is_null($this->newWidth)) {
                $this->newWidth  = round($this->newWidth * $this->dpr);
                $this->log("Setting new width based on dpr={$this->dpr} - w={$this->newWidth}");
            }
            if (!is_null($this->newHeight)) {
                $this->newHeight = round($this->newHeight * $this->dpr);
                $this->log("Setting new height based on dpr={$this->dpr} - h={$this->newHeight}");
            }
        }

        // Check values to be within domain
        is_null($this->newWidth)
            or is_numeric($this->newWidth)
            or $this->raiseError('Width not numeric');

        is_null($this->newHeight)
            or is_numeric($this->newHeight)
            or $this->raiseError('Height not numeric');

        $this->log("Init dimension (after) newWidth x newHeight is {$this->newWidth} x {$this->newHeight}.");

        return $this;
    }



    /**
     * Calculate new width and height of image, based on settings.
     *
     * @return $this
     */
    public function calculateNewWidthAndHeight()
    {
        // Crop, use cropped width and height as base for calulations
        $this->log("Calculate new width and height.");
        $this->log("Original width x height is {$this->width} x {$this->height}.");
        $this->log("Target dimension (before calculating) newWidth x newHeight is {$this->newWidth} x {$this->newHeight}.");

        // Check if there is an area to crop off
        if (isset($this->area)) {
            $this->offset['top']    = round($this->area['top'] / 100 * $this->height);
            $this->offset['right']  = round($this->area['right'] / 100 * $this->width);
            $this->offset['bottom'] = round($this->area['bottom'] / 100 * $this->height);
            $this->offset['left']   = round($this->area['left'] / 100 * $this->width);
            $this->offset['width']  = $this->width - $this->offset['left'] - $this->offset['right'];
            $this->offset['height'] = $this->height - $this->offset['top'] - $this->offset['bottom'];
            $this->width  = $this->offset['width'];
            $this->height = $this->offset['height'];
            $this->log("The offset for the area to use is top {$this->area['top']}%, right {$this->area['right']}%, bottom {$this->area['bottom']}%, left {$this->area['left']}%.");
            $this->log("The offset for the area to use is top {$this->offset['top']}px, right {$this->offset['right']}px, bottom {$this->offset['bottom']}px, left {$this->offset['left']}px, width {$this->offset['width']}px, height {$this->offset['height']}px.");
        }

        $width  = $this->width;
        $height = $this->height;

        // Check if crop is set
        if ($this->crop) {
            $width  = $this->crop['width']  = $this->crop['width'] <= 0 ? $this->width + $this->crop['width'] : $this->crop['width'];
            $height = $this->crop['height'] = $this->crop['height'] <= 0 ? $this->height + $this->crop['height'] : $this->crop['height'];

            if ($this->crop['start_x'] == 'left') {
                $this->crop['start_x'] = 0;
            } elseif ($this->crop['start_x'] == 'right') {
                $this->crop['start_x'] = $this->width - $width;
            } elseif ($this->crop['start_x'] == 'center') {
                $this->crop['start_x'] = round($this->width / 2) - round($width / 2);
            }

            if ($this->crop['start_y'] == 'top') {
                $this->crop['start_y'] = 0;
            } elseif ($this->crop['start_y'] == 'bottom') {
                $this->crop['start_y'] = $this->height - $height;
            } elseif ($this->crop['start_y'] == 'center') {
                $this->crop['start_y'] = round($this->height / 2) - round($height / 2);
            }

            $this->log("Crop area is width {$width}px, height {$height}px, start_x {$this->crop['start_x']}px, start_y {$this->crop['start_y']}px.");
        }

        // Calculate new width and height if keeping aspect-ratio.
        if ($this->keepRatio) {

            $this->log("Keep aspect ratio.");

            // Crop-to-fit and both new width and height are set.
            if (($this->cropToFit || $this->fillToFit) && isset($this->newWidth) && isset($this->newHeight)) {

                // Use newWidth and newHeigh as width/height, image should fit in box.
                $this->log("Use newWidth and newHeigh as width/height, image should fit in box.");
            } elseif (isset($this->newWidth) && isset($this->newHeight)) {

                // Both new width and height are set.
                // Use newWidth and newHeigh as max width/height, image should not be larger.
                $ratioWidth  = $width  / $this->newWidth;
                $ratioHeight = $height / $this->newHeight;
                $ratio = ($ratioWidth > $ratioHeight) ? $ratioWidth : $ratioHeight;
                $this->newWidth  = round($width  / $ratio);
                $this->newHeight = round($height / $ratio);
                $this->log("New width and height was set.");
            } elseif (isset($this->newWidth)) {

                // Use new width as max-width
                $factor = (float)$this->newWidth / (float)$width;
                $this->newHeight = round($factor * $height);
                $this->log("New width was set.");
            } elseif (isset($this->newHeight)) {

                // Use new height as max-hight
                $factor = (float)$this->newHeight / (float)$height;
                $this->newWidth = round($factor * $width);
                $this->log("New height was set.");
            } else {

                // Use existing width and height as new width and height.
                $this->newWidth = $width;
                $this->newHeight = $height;
            }


            // Get image dimensions for pre-resize image.
            if ($this->cropToFit || $this->fillToFit) {

                // Get relations of original & target image
                $ratioWidth  = $width  / $this->newWidth;
                $ratioHeight = $height / $this->newHeight;

                if ($this->cropToFit) {

                    // Use newWidth and newHeigh as defined width/height,
                    // image should fit the area.
                    $this->log("Crop to fit.");
                    $ratio = ($ratioWidth < $ratioHeight) ? $ratioWidth : $ratioHeight;
                    $this->cropWidth  = round($width  / $ratio);
                    $this->cropHeight = round($height / $ratio);
                    $this->log("Crop width, height, ratio: $this->cropWidth x $this->cropHeight ($ratio).");
                } elseif ($this->fillToFit) {

                    // Use newWidth and newHeigh as defined width/height,
                    // image should fit the area.
                    $this->log("Fill to fit.");
                    $ratio = ($ratioWidth < $ratioHeight) ? $ratioHeight : $ratioWidth;
                    $this->fillWidth  = round($width  / $ratio);
                    $this->fillHeight = round($height / $ratio);
                    $this->log("Fill width, height, ratio: $this->fillWidth x $this->fillHeight ($ratio).");
                }
            }
        }

        // Crop, ensure to set new width and height
        if ($this->crop) {
            $this->log("Crop.");
            $this->newWidth = round(isset($this->newWidth) ? $this->newWidth : $this->crop['width']);
            $this->newHeight = round(isset($this->newHeight) ? $this->newHeight : $this->crop['height']);
        }

        // Fill to fit, ensure to set new width and height
        /*if ($this->fillToFit) {
            $this->log("FillToFit.");
            $this->newWidth = round(isset($this->newWidth) ? $this->newWidth : $this->crop['width']);
            $this->newHeight = round(isset($this->newHeight) ? $this->newHeight : $this->crop['height']);
        }*/

        // No new height or width is set, use existing measures.
        $this->newWidth  = round(isset($this->newWidth) ? $this->newWidth : $this->width);
        $this->newHeight = round(isset($this->newHeight) ? $this->newHeight : $this->height);
        $this->log("Calculated new width x height as {$this->newWidth} x {$this->newHeight}.");

        return $this;
    }



    /**
     * Re-calculate image dimensions when original image dimension has changed.
     *
     * @return $this
     */
    public function reCalculateDimensions()
    {
        $this->log("Re-calculate image dimensions, newWidth x newHeigh was: " . $this->newWidth . " x " . $this->newHeight);

        $this->newWidth  = $this->newWidthOrig;
        $this->newHeight = $this->newHeightOrig;
        $this->crop      = $this->cropOrig;

        $this->initDimensions()
            ->calculateNewWidthAndHeight();

        return $this;
    }



    /**
     * Set extension for filename to save as.
     *
     * @param string $saveas extension to save image as
     *
     * @return $this
     */
    public function setSaveAsExtension($saveAs = null)
    {
        if (isset($saveAs)) {
            $saveAs = strtolower($saveAs);
            $this->checkFileExtension($saveAs);
            $this->saveAs = $saveAs;
            $this->extension = $saveAs;
        }

        $this->log("Prepare to save image as: " . $this->extension);

        return $this;
    }



    /**
     * Set JPEG quality to use when saving image
     *
     * @param int $quality as the quality to set.
     *
     * @return $this
     */
    public function setJpegQuality($quality = null)
    {
        if ($quality) {
            $this->useQuality = true;
        }

        $this->quality = isset($quality)
            ? $quality
            : self::JPEG_QUALITY_DEFAULT;

        (is_numeric($this->quality) and $this->quality > 0 and $this->quality <= 100)
            or $this->raiseError('Quality not in range.');

        $this->log("Setting JPEG quality to {$this->quality}.");

        return $this;
    }



    /**
     * Set PNG compressen algorithm to use when saving image
     *
     * @param int $compress as the algorithm to use.
     *
     * @return $this
     */
    public function setPngCompression($compress = null)
    {
        if ($compress) {
            $this->useCompress = true;
        }

        $this->compress = isset($compress)
            ? $compress
            : self::PNG_COMPRESSION_DEFAULT;

        (is_numeric($this->compress) and $this->compress >= -1 and $this->compress <= 9)
            or $this->raiseError('Quality not in range.');

        $this->log("Setting PNG compression level to {$this->compress}.");

        return $this;
    }



    /**
     * Use original image if possible, check options which affects image processing.
     *
     * @param boolean $useOrig default is to use original if possible, else set to false.
     *
     * @return $this
     */
    public function useOriginalIfPossible($useOrig = true)
    {
        if (
            $useOrig
            && ($this->newWidth == $this->width)
            && ($this->newHeight == $this->height)
            && !$this->area
            && !$this->crop
            && !$this->cropToFit
            && !$this->fillToFit
            && !$this->filters
            && !$this->sharpen
            && !$this->emboss
            && !$this->blur
            && !$this->convolve
            && !$this->palette
            && !$this->useQuality
            && !$this->useCompress
            && !$this->saveAs
            && !$this->rotateBefore
            && !$this->rotateAfter
            && !$this->autoRotate
            && !$this->bgColor
            && ($this->upscale === self::UPSCALE_DEFAULT)
            && !$this->lossy
        ) {
            $this->log("Using original image.");
            $this->output($this->pathToImage);
        }

        return $this;
    }



    /**
     * Generate filename to save file in cache.
     *
     * @param string  $base      as optional basepath for storing file.
     * @param boolean $useSubdir use or skip the subdir part when creating the
     *                           filename.
     * @param string  $prefix    to add as part of filename
     *
     * @return $this
     */
    public function generateFilename($base = null, $useSubdir = true, $prefix = null)
    {
        $filename     = basename($this->pathToImage);
        $cropToFit    = $this->cropToFit    ? '_cf'                      : null;
        $fillToFit    = $this->fillToFit    ? '_ff'                      : null;
        $crop_x       = $this->crop_x       ? "_x{$this->crop_x}"        : null;
        $crop_y       = $this->crop_y       ? "_y{$this->crop_y}"        : null;
        $scale        = $this->scale        ? "_s{$this->scale}"         : null;
        $bgColor      = $this->bgColor      ? "_bgc{$this->bgColor}"     : null;
        $quality      = $this->quality      ? "_q{$this->quality}"       : null;
        $compress     = $this->compress     ? "_co{$this->compress}"     : null;
        $rotateBefore = $this->rotateBefore ? "_rb{$this->rotateBefore}" : null;
        $rotateAfter  = $this->rotateAfter  ? "_ra{$this->rotateAfter}"  : null;
        $lossy        = $this->lossy        ? "_l"                       : null;
        $interlace    = $this->interlace    ? "_i"                       : null;

        $saveAs = $this->normalizeFileExtension();
        $saveAs = $saveAs ? "_$saveAs" : null;

        $copyStrat = null;
        if ($this->copyStrategy === self::RESIZE) {
            $copyStrat = "_rs";
        }

        $width  = $this->newWidth  ? '_' . $this->newWidth  : null;
        $height = $this->newHeight ? '_' . $this->newHeight : null;

        $offset = isset($this->offset)
            ? '_o' . $this->offset['top'] . '-' . $this->offset['right'] . '-' . $this->offset['bottom'] . '-' . $this->offset['left']
            : null;

        $crop = $this->crop
            ? '_c' . $this->crop['width'] . '-' . $this->crop['height'] . '-' . $this->crop['start_x'] . '-' . $this->crop['start_y']
            : null;

        $filters = null;
        if (isset($this->filters)) {
            foreach ($this->filters as $filter) {
                if (is_array($filter)) {
                    $filters .= "_f{$filter['id']}";
                    for ($i = 1; $i <= $filter['argc']; $i++) {
                        $filters .= "-" . $filter["arg{$i}"];
                    }
                }
            }
        }

        $sharpen = $this->sharpen ? 's' : null;
        $emboss  = $this->emboss  ? 'e' : null;
        $blur    = $this->blur    ? 'b' : null;
        $palette = $this->palette ? 'p' : null;

        $autoRotate = $this->autoRotate ? 'ar' : null;

        $optimize  = $this->jpegOptimize ? 'o' : null;
        $optimize .= $this->pngFilter    ? 'f' : null;
        $optimize .= $this->pngDeflate   ? 'd' : null;

        $convolve = null;
        if ($this->convolve) {
            $convolve = '_conv' . preg_replace('/[^a-zA-Z0-9]/', '', $this->convolve);
        }

        $upscale = null;
        if ($this->upscale !== self::UPSCALE_DEFAULT) {
            $upscale = '_nu';
        }

        $subdir = null;
        if ($useSubdir === true) {
            $subdir = str_replace('/', '-', dirname($this->imageSrc));
            $subdir = ($subdir == '.') ? '_.' : $subdir;
            $subdir .= '_';
        }

        $file = $prefix . $subdir . $filename . $width . $height
            . $offset . $crop . $cropToFit . $fillToFit
            . $crop_x . $crop_y . $upscale
            . $quality . $filters . $sharpen . $emboss . $blur . $palette
            . $optimize . $compress
            . $scale . $rotateBefore . $rotateAfter . $autoRotate . $bgColor
            . $convolve . $copyStrat . $lossy . $interlace . $saveAs;

        return $this->setTarget($file, $base);
    }



    /**
     * Use cached version of image, if possible.
     *
     * @param boolean $useCache is default true, set to false to avoid using cached object.
     *
     * @return $this
     */
    public function useCacheIfPossible($useCache = true)
    {
        if ($useCache && is_readable($this->cacheFileName)) {
            $fileTime   = filemtime($this->pathToImage);
            $cacheTime  = filemtime($this->cacheFileName);

            if ($fileTime <= $cacheTime) {
                if ($this->useCache) {
                    if ($this->verbose) {
                        $this->log("Use cached file.");
                        $this->log("Cached image filesize: " . filesize($this->cacheFileName) . " bytes.");
                    }
                    $this->output($this->cacheFileName, $this->outputFormat);
                } else {
                    $this->log("Cache is valid but ignoring it by intention.");
                }
            } else {
                $this->log("Original file is modified, ignoring cache.");
            }
        } else {
            $this->log("Cachefile does not exists or ignoring it.");
        }

        return $this;
    }



    /**
     * Load image from disk. Try to load image without verbose error message,
     * if fail, load again and display error messages.
     *
     * @param string $src of image.
     * @param string $dir as base directory where images are.
     *
     * @return $this
     *
     */
    public function load($src = null, $dir = null)
    {
        if (isset($src)) {
            $this->setSource($src, $dir);
        }

        $this->loadImageDetails();

        if ($this->fileType === IMG_WEBP) {
            $this->image = imagecreatefromwebp($this->pathToImage);
        } else {
            $imageAsString = file_get_contents($this->pathToImage);
            $this->image = imagecreatefromstring($imageAsString);
        }
        if ($this->image === false) {
            throw new Exception("Could not load image.");
        }

        /* Removed v0.7.7
        if (image_type_to_mime_type($this->fileType) == 'image/png') {
            $type = $this->getPngType();
            $hasFewColors = imagecolorstotal($this->image);

            if ($type == self::PNG_RGB_PALETTE || ($hasFewColors > 0 && $hasFewColors <= 256)) {
                if ($this->verbose) {
                    $this->log("Handle this image as a palette image.");
                }
                $this->palette = true;
            }
        }
        */

        if ($this->verbose) {
            $this->log("### Image successfully loaded from file.");
            $this->log(" imageistruecolor() : " . (imageistruecolor($this->image) ? 'true' : 'false'));
            $this->log(" imagecolorstotal() : " . imagecolorstotal($this->image));
            $this->log(" Number of colors in image = " . $this->colorsTotal($this->image));
            $index = imagecolortransparent($this->image);
            $this->log(" Detected transparent color = " . ($index >= 0 ? implode(", ", imagecolorsforindex($this->image, $index)) : "NONE") . " at index = $index");
        }

        return $this;
    }



    /**
     * Get the type of PNG image.
     *
     * @param string $filename to use instead of default.
     *
     * @return int as the type of the png-image
     *
     */
    public function getPngType($filename = null)
    {
        $filename = $filename ? $filename : $this->pathToImage;

        $pngType = ord(file_get_contents($filename, false, null, 25, 1));

        if ($this->verbose) {
            $this->log("Checking png type of: " . $filename);
            $this->log($this->getPngTypeAsString($pngType));
        }

        return $pngType;
    }



    /**
     * Get the type of PNG image as a verbose string.
     *
     * @param integer $type     to use, default is to check the type.
     * @param string  $filename to use instead of default.
     *
     * @return int as the type of the png-image
     *
     */
    private function getPngTypeAsString($pngType = null, $filename = null)
    {
        if ($filename || !$pngType) {
            $pngType = $this->getPngType($filename);
        }

        $index = imagecolortransparent($this->image);
        $transparent = null;
        if ($index != -1) {
            $transparent = " (transparent)";
        }

        switch ($pngType) {

            case self::PNG_GREYSCALE:
                $text = "PNG is type 0, Greyscale$transparent";
                break;

            case self::PNG_RGB:
                $text = "PNG is type 2, RGB$transparent";
                break;

            case self::PNG_RGB_PALETTE:
                $text = "PNG is type 3, RGB with palette$transparent";
                break;

            case self::PNG_GREYSCALE_ALPHA:
                $text = "PNG is type 4, Greyscale with alpha channel";
                break;

            case self::PNG_RGB_ALPHA:
                $text = "PNG is type 6, RGB with alpha channel (PNG 32-bit)";
                break;

            default:
                $text = "PNG is UNKNOWN type, is it really a PNG image?";
        }

        return $text;
    }




    /**
     * Calculate number of colors in an image.
     *
     * @param resource $im the image.
     *
     * @return int
     */
    private function colorsTotal($im)
    {
        if (imageistruecolor($im)) {
            $this->log("Colors as true color.");
            $h = imagesy($im);
            $w = imagesx($im);
            $c = array();
            for ($x = 0; $x < $w; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    @$c['c' . imagecolorat($im, $x, $y)]++;
                }
            }
            return count($c);
        } else {
            $this->log("Colors as palette.");
            return imagecolorstotal($im);
        }
    }



    /**
     * Preprocess image before rezising it.
     *
     * @return $this
     */
    public function preResize()
    {
        $this->log("### Pre-process before resizing");

        // Rotate image
        if ($this->rotateBefore) {
            $this->log("Rotating image.");
            $this->rotate($this->rotateBefore, $this->bgColor)
                ->reCalculateDimensions();
        }

        // Auto-rotate image
        if ($this->autoRotate) {
            $this->log("Auto rotating image.");
            $this->rotateExif()
                ->reCalculateDimensions();
        }

        // Scale the original image before starting
        if (isset($this->scale)) {
            $this->log("Scale by {$this->scale}%");
            $newWidth  = $this->width * $this->scale / 100;
            $newHeight = $this->height * $this->scale / 100;
            $img = $this->CreateImageKeepTransparency($newWidth, $newHeight);
            imagecopyresampled($img, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);
            $this->image = $img;
            $this->width = $newWidth;
            $this->height = $newHeight;
        }

        return $this;
    }



    /**
     * Resize or resample the image while resizing.
     *
     * @param int $strategy as CImage::RESIZE or CImage::RESAMPLE
     *
     * @return $this
     */
    public function setCopyResizeStrategy($strategy)
    {
        $this->copyStrategy = $strategy;
        return $this;
    }



    /**
     * Resize and or crop the image.
     *
     * @return void
     */
    public function imageCopyResampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        if ($this->copyStrategy == self::RESIZE) {
            $this->log("Copy by resize");
            imagecopyresized($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        } else {
            $this->log("Copy by resample");
            imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        }
    }



    /**
     * Resize and or crop the image.
     *
     * @return $this
     */
    public function resize()
    {

        $this->log("### Starting to Resize()");
        $this->log("Upscale = '$this->upscale'");

        // Only use a specified area of the image, $this->offset is defining the area to use
        if (isset($this->offset)) {

            $this->log("Offset for area to use, cropping it width={$this->offset['width']}, height={$this->offset['height']}, start_x={$this->offset['left']}, start_y={$this->offset['top']}");
            $img = $this->CreateImageKeepTransparency($this->offset['width'], $this->offset['height']);
            imagecopy($img, $this->image, 0, 0, $this->offset['left'], $this->offset['top'], $this->offset['width'], $this->offset['height']);
            $this->image = $img;
            $this->width = $this->offset['width'];
            $this->height = $this->offset['height'];
        }

        if ($this->crop) {

            // Do as crop, take only part of image
            $this->log("Cropping area width={$this->crop['width']}, height={$this->crop['height']}, start_x={$this->crop['start_x']}, start_y={$this->crop['start_y']}");
            $img = $this->CreateImageKeepTransparency($this->crop['width'], $this->crop['height']);
            imagecopy($img, $this->image, 0, 0, $this->crop['start_x'], $this->crop['start_y'], $this->crop['width'], $this->crop['height']);
            $this->image = $img;
            $this->width = $this->crop['width'];
            $this->height = $this->crop['height'];
        }

        if (!$this->upscale) {
            // Consider rewriting the no-upscale code to fit within this if-statement,
            // likely to be more readable code.
            // The code is more or leass equal in below crop-to-fit, fill-to-fit and stretch
        }

        if ($this->cropToFit) {

            // Resize by crop to fit
            $this->log("Resizing using strategy - Crop to fit");

            if (
                !$this->upscale
                && ($this->width < $this->newWidth || $this->height < $this->newHeight)
            ) {
                $this->log("Resizing - smaller image, do not upscale.");

                $posX = 0;
                $posY = 0;
                $cropX = 0;
                $cropY = 0;

                if ($this->newWidth > $this->width) {
                    $posX = round(($this->newWidth - $this->width) / 2);
                }
                if ($this->newWidth < $this->width) {
                    $cropX = round(($this->width / 2) - ($this->newWidth / 2));
                }

                if ($this->newHeight > $this->height) {
                    $posY = round(($this->newHeight - $this->height) / 2);
                }
                if ($this->newHeight < $this->height) {
                    $cropY = round(($this->height / 2) - ($this->newHeight / 2));
                }
                $this->log(" cwidth: $this->cropWidth");
                $this->log(" cheight: $this->cropHeight");
                $this->log(" nwidth: $this->newWidth");
                $this->log(" nheight: $this->newHeight");
                $this->log(" width: $this->width");
                $this->log(" height: $this->height");
                $this->log(" posX: $posX");
                $this->log(" posY: $posY");
                $this->log(" cropX: $cropX");
                $this->log(" cropY: $cropY");

                $imageResized = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);
                imagecopy($imageResized, $this->image, $posX, $posY, $cropX, $cropY, $this->width, $this->height);
            } else {
                $cropX = round(($this->cropWidth / 2) - ($this->newWidth / 2));
                $cropY = round(($this->cropHeight / 2) - ($this->newHeight / 2));
                $imgPreCrop   = $this->CreateImageKeepTransparency($this->cropWidth, $this->cropHeight);
                $imageResized = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);
                $this->imageCopyResampled($imgPreCrop, $this->image, 0, 0, 0, 0, $this->cropWidth, $this->cropHeight, $this->width, $this->height);
                imagecopy($imageResized, $imgPreCrop, 0, 0, $cropX, $cropY, $this->newWidth, $this->newHeight);
            }

            $this->image = $imageResized;
            $this->width = $this->newWidth;
            $this->height = $this->newHeight;
        } elseif ($this->fillToFit) {

            // Resize by fill to fit
            $this->log("Resizing using strategy - Fill to fit");

            $posX = 0;
            $posY = 0;

            $ratioOrig = $this->width / $this->height;
            $ratioNew  = $this->newWidth / $this->newHeight;

            // Check ratio for landscape or portrait
            if ($ratioOrig < $ratioNew) {
                $posX = round(($this->newWidth - $this->fillWidth) / 2);
            } else {
                $posY = round(($this->newHeight - $this->fillHeight) / 2);
            }

            if (
                !$this->upscale
                && ($this->width < $this->newWidth && $this->height < $this->newHeight)
            ) {

                $this->log("Resizing - smaller image, do not upscale.");
                $posX = round(($this->newWidth - $this->width) / 2);
                $posY = round(($this->newHeight - $this->height) / 2);
                $imageResized = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);
                imagecopy($imageResized, $this->image, $posX, $posY, 0, 0, $this->width, $this->height);
            } else {
                $imgPreFill   = $this->CreateImageKeepTransparency($this->fillWidth, $this->fillHeight);
                $imageResized = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);
                $this->imageCopyResampled($imgPreFill, $this->image, 0, 0, 0, 0, $this->fillWidth, $this->fillHeight, $this->width, $this->height);
                imagecopy($imageResized, $imgPreFill, $posX, $posY, 0, 0, $this->fillWidth, $this->fillHeight);
            }

            $this->image = $imageResized;
            $this->width = $this->newWidth;
            $this->height = $this->newHeight;
        } elseif (!($this->newWidth == $this->width && $this->newHeight == $this->height)) {

            // Resize it
            $this->log("Resizing, new height and/or width");

            if (
                !$this->upscale
                && ($this->width < $this->newWidth || $this->height < $this->newHeight)
            ) {
                $this->log("Resizing - smaller image, do not upscale.");

                if (!$this->keepRatio) {
                    $this->log("Resizing - stretch to fit selected.");

                    $posX = 0;
                    $posY = 0;
                    $cropX = 0;
                    $cropY = 0;

                    if ($this->newWidth > $this->width && $this->newHeight > $this->height) {
                        $posX = round(($this->newWidth - $this->width) / 2);
                        $posY = round(($this->newHeight - $this->height) / 2);
                    } elseif ($this->newWidth > $this->width) {
                        $posX = round(($this->newWidth - $this->width) / 2);
                        $cropY = round(($this->height - $this->newHeight) / 2);
                    } elseif ($this->newHeight > $this->height) {
                        $posY = round(($this->newHeight - $this->height) / 2);
                        $cropX = round(($this->width - $this->newWidth) / 2);
                    }

                    $imageResized = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);
                    imagecopy($imageResized, $this->image, $posX, $posY, $cropX, $cropY, $this->width, $this->height);
                    $this->image = $imageResized;
                    $this->width = $this->newWidth;
                    $this->height = $this->newHeight;
                }
            } else {
                $imageResized = $this->CreateImageKeepTransparency($this->newWidth, $this->newHeight);
                $this->imageCopyResampled($imageResized, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width, $this->height);
                $this->image = $imageResized;
                $this->width = $this->newWidth;
                $this->height = $this->newHeight;
            }
        }

        return $this;
    }



    /**
     * Postprocess image after rezising image.
     *
     * @return $this
     */
    public function postResize()
    {
        $this->log("### Post-process after resizing");

        // Rotate image
        if ($this->rotateAfter) {
            $this->log("Rotating image.");
            $this->rotate($this->rotateAfter, $this->bgColor);
        }

        // Apply filters
        if (isset($this->filters) && is_array($this->filters)) {

            foreach ($this->filters as $filter) {
                $this->log("Applying filter {$filter['type']}.");

                switch ($filter['argc']) {

                    case 0:
                        imagefilter($this->image, $filter['type']);
                        break;

                    case 1:
                        imagefilter($this->image, $filter['type'], $filter['arg1']);
                        break;

                    case 2:
                        imagefilter($this->image, $filter['type'], $filter['arg1'], $filter['arg2']);
                        break;

                    case 3:
                        imagefilter($this->image, $filter['type'], $filter['arg1'], $filter['arg2'], $filter['arg3']);
                        break;

                    case 4:
                        imagefilter($this->image, $filter['type'], $filter['arg1'], $filter['arg2'], $filter['arg3'], $filter['arg4']);
                        break;
                }
            }
        }

        // Convert to palette image
        if ($this->palette) {
            $this->log("Converting to palette image.");
            $this->trueColorToPalette();
        }

        // Blur the image
        if ($this->blur) {
            $this->log("Blur.");
            $this->blurImage();
        }

        // Emboss the image
        if ($this->emboss) {
            $this->log("Emboss.");
            $this->embossImage();
        }

        // Sharpen the image
        if ($this->sharpen) {
            $this->log("Sharpen.");
            $this->sharpenImage();
        }

        // Custom convolution
        if ($this->convolve) {
            //$this->log("Convolve: " . $this->convolve);
            $this->imageConvolution();
        }

        return $this;
    }



    /**
     * Rotate image using angle.
     *
     * @param float $angle        to rotate image.
     * @param int   $anglebgColor to fill image with if needed.
     *
     * @return $this
     */
    public function rotate($angle, $bgColor)
    {
        $this->log("Rotate image " . $angle . " degrees with filler color.");

        $color = $this->getBackgroundColor();
        $this->image = imagerotate($this->image, $angle, $color);

        $this->width  = imagesx($this->image);
        $this->height = imagesy($this->image);

        $this->log("New image dimension width x height: " . $this->width . " x " . $this->height);

        return $this;
    }



    /**
     * Rotate image using information in EXIF.
     *
     * @return $this
     */
    public function rotateExif()
    {
        if (!in_array($this->fileType, array(IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM))) {
            $this->log("Autorotate ignored, EXIF not supported by this filetype.");
            return $this;
        }

        $exif = exif_read_data($this->pathToImage);

        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $this->log("Autorotate 180.");
                    $this->rotate(180, $this->bgColor);
                    break;

                case 6:
                    $this->log("Autorotate -90.");
                    $this->rotate(-90, $this->bgColor);
                    break;

                case 8:
                    $this->log("Autorotate 90.");
                    $this->rotate(90, $this->bgColor);
                    break;

                default:
                    $this->log("Autorotate ignored, unknown value as orientation.");
            }
        } else {
            $this->log("Autorotate ignored, no orientation in EXIF.");
        }

        return $this;
    }



    /**
     * Convert true color image to palette image, keeping alpha.
     * http://stackoverflow.com/questions/5752514/how-to-convert-png-to-8-bit-png-using-php-gd-library
     *
     * @return void
     */
    public function trueColorToPalette()
    {
        $img = imagecreatetruecolor($this->width, $this->height);
        $bga = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagecolortransparent($img, $bga);
        imagefill($img, 0, 0, $bga);
        imagecopy($img, $this->image, 0, 0, 0, 0, $this->width, $this->height);
        imagetruecolortopalette($img, false, 255);
        imagesavealpha($img, true);

        if (imageistruecolor($this->image)) {
            $this->log("Matching colors with true color image.");
            imagecolormatch($this->image, $img);
        }

        $this->image = $img;
    }



    /**
     * Sharpen image using image convolution.
     *
     * @return $this
     */
    public function sharpenImage()
    {
        $this->imageConvolution('sharpen');
        return $this;
    }



    /**
     * Emboss image using image convolution.
     *
     * @return $this
     */
    public function embossImage()
    {
        $this->imageConvolution('emboss');
        return $this;
    }



    /**
     * Blur image using image convolution.
     *
     * @return $this
     */
    public function blurImage()
    {
        $this->imageConvolution('blur');
        return $this;
    }



    /**
     * Create convolve expression and return arguments for image convolution.
     *
     * @param string $expression constant string which evaluates to a list of
     *                           11 numbers separated by komma or such a list.
     *
     * @return array as $matrix (3x3), $divisor and $offset
     */
    public function createConvolveArguments($expression)
    {
        // Check of matching constant
        if (isset($this->convolves[$expression])) {
            $expression = $this->convolves[$expression];
        }

        $part = explode(',', $expression);
        $this->log("Creating convolution expressen: $expression");

        // Expect list of 11 numbers, split by , and build up arguments
        if (count($part) != 11) {
            throw new Exception(
                "Missmatch in argument convolve. Expected comma-separated string with
                11 float values. Got $expression."
            );
        }

        array_walk($part, function ($item, $key) {
            if (!is_numeric($item)) {
                throw new Exception("Argument to convolve expression should be float but is not.");
            }
        });

        return array(
            array(
                array($part[0], $part[1], $part[2]),
                array($part[3], $part[4], $part[5]),
                array($part[6], $part[7], $part[8]),
            ),
            $part[9],
            $part[10],
        );
    }



    /**
     * Add custom expressions (or overwrite existing) for image convolution.
     *
     * @param array $options Key value array with strings to be converted
     *                       to convolution expressions.
     *
     * @return $this
     */
    public function addConvolveExpressions($options)
    {
        $this->convolves = array_merge($this->convolves, $options);
        return $this;
    }



    /**
     * Image convolution.
     *
     * @param string $options A string with 11 float separated by comma.
     *
     * @return $this
     */
    public function imageConvolution($options = null)
    {
        // Use incoming options or use $this.
        $options = $options ? $options : $this->convolve;

        // Treat incoming as string, split by +
        $this->log("Convolution with '$options'");
        $options = explode(":", $options);

        // Check each option if it matches constant value
        foreach ($options as $option) {
            list($matrix, $divisor, $offset) = $this->createConvolveArguments($option);
            imageconvolution($this->image, $matrix, $divisor, $offset);
        }

        return $this;
    }



    /**
     * Set default background color between 000000-FFFFFF or if using
     * alpha 00000000-FFFFFF7F.
     *
     * @param string $color as hex value.
     *
     * @return $this
     */
    public function setDefaultBackgroundColor($color)
    {
        $this->log("Setting default background color to '$color'.");

        if (!(strlen($color) == 6 || strlen($color) == 8)) {
            throw new Exception(
                "Background color needs a hex value of 6 or 8
                digits. 000000-FFFFFF or 00000000-FFFFFF7F.
                Current value was: '$color'."
            );
        }

        $red    = hexdec(substr($color, 0, 2));
        $green  = hexdec(substr($color, 2, 2));
        $blue   = hexdec(substr($color, 4, 2));

        $alpha = (strlen($color) == 8)
            ? hexdec(substr($color, 6, 2))
            : null;

        if (($red < 0 || $red > 255)
            || ($green < 0 || $green > 255)
            || ($blue < 0 || $blue > 255)
            || ($alpha < 0 || $alpha > 127)
        ) {
            throw new Exception(
                "Background color out of range. Red, green blue
                should be 00-FF and alpha should be 00-7F.
                Current value was: '$color'."
            );
        }

        $this->bgColor = strtolower($color);
        $this->bgColorDefault = array(
            'red'   => $red,
            'green' => $green,
            'blue'  => $blue,
            'alpha' => $alpha
        );

        return $this;
    }



    /**
     * Get the background color.
     *
     * @param resource $img the image to work with or null if using $this->image.
     *
     * @return color value or null if no background color is set.
     */
    private function getBackgroundColor($img = null)
    {
        $img = isset($img) ? $img : $this->image;

        if ($this->bgColorDefault) {

            $red   = $this->bgColorDefault['red'];
            $green = $this->bgColorDefault['green'];
            $blue  = $this->bgColorDefault['blue'];
            $alpha = $this->bgColorDefault['alpha'];

            if ($alpha) {
                $color = imagecolorallocatealpha($img, $red, $green, $blue, $alpha);
            } else {
                $color = imagecolorallocate($img, $red, $green, $blue);
            }

            return $color;
        } else {
            return 0;
        }
    }



    /**
     * Create a image and keep transparency for png and gifs.
     *
     * @param int $width of the new image.
     * @param int $height of the new image.
     *
     * @return image resource.
     */
    private function createImageKeepTransparency($width, $height)
    {
        $this->log("Creating a new working image width={$width}px, height={$height}px.");
        $img = imagecreatetruecolor($width, $height);
        imagealphablending($img, false);
        imagesavealpha($img, true);

        $index = $this->image
            ? imagecolortransparent($this->image)
            : -1;

        if ($index != -1) {

            imagealphablending($img, true);
            $transparent = imagecolorsforindex($this->image, $index);
            $color = imagecolorallocatealpha($img, $transparent['red'], $transparent['green'], $transparent['blue'], $transparent['alpha']);
            imagefill($img, 0, 0, $color);
            $index = imagecolortransparent($img, $color);
            $this->Log("Detected transparent color = " . implode(", ", $transparent) . " at index = $index");
        } elseif ($this->bgColorDefault) {

            $color = $this->getBackgroundColor($img);
            imagefill($img, 0, 0, $color);
            $this->Log("Filling image with background color.");
        }

        return $img;
    }



    /**
     * Set optimizing  and post-processing options.
     *
     * @param array $options with config for postprocessing with external tools.
     *
     * @return $this
     */
    public function setPostProcessingOptions($options)
    {
        if (isset($options['jpeg_optimize']) && $options['jpeg_optimize']) {
            $this->jpegOptimizeCmd = $options['jpeg_optimize_cmd'];
        } else {
            $this->jpegOptimizeCmd = null;
        }

        if (
            array_key_exists("png_lossy", $options)
            && $options['png_lossy'] !== false
        ) {
            $this->pngLossy = $options['png_lossy'];
            $this->pngLossyCmd = $options['png_lossy_cmd'];
        } else {
            $this->pngLossyCmd = null;
        }

        if (isset($options['png_filter']) && $options['png_filter']) {
            $this->pngFilterCmd = $options['png_filter_cmd'];
        } else {
            $this->pngFilterCmd = null;
        }

        if (isset($options['png_deflate']) && $options['png_deflate']) {
            $this->pngDeflateCmd = $options['png_deflate_cmd'];
        } else {
            $this->pngDeflateCmd = null;
        }

        return $this;
    }



    /**
     * Find out the type (file extension) for the image to be saved.
     *
     * @return string as image extension.
     */
    protected function getTargetImageExtension()
    {
        // switch on mimetype
        if (isset($this->extension)) {
            return strtolower($this->extension);
        } elseif ($this->fileType === IMG_WEBP) {
            return "webp";
        }

        return substr(image_type_to_extension($this->fileType), 1);
    }



    /**
     * Save image.
     *
     * @param string  $src       as target filename.
     * @param string  $base      as base directory where to store images.
     * @param boolean $overwrite or not, default to always overwrite file.
     *
     * @return $this or false if no folder is set.
     */
    public function save($src = null, $base = null, $overwrite = true)
    {
        if (isset($src)) {
            $this->setTarget($src, $base);
        }

        if ($overwrite === false && is_file($this->cacheFileName)) {
            $this->Log("Not overwriting file since its already exists and \$overwrite if false.");
            return;
        }

        is_writable($this->saveFolder)
            or $this->raiseError('Target directory is not writable.');

        $type = $this->getTargetImageExtension();
        $this->Log("Saving image as " . $type);
        switch ($type) {

            case 'jpeg':
            case 'jpg':
                // Set as interlaced progressive JPEG
                if ($this->interlace) {
                    $this->Log("Set JPEG image to be interlaced.");
                    $res = imageinterlace($this->image, true);
                }

                $this->Log("Saving image as JPEG to cache using quality = {$this->quality}.");
                imagejpeg($this->image, $this->cacheFileName, $this->quality);

                // Use JPEG optimize if defined
                if ($this->jpegOptimizeCmd) {
                    if ($this->verbose) {
                        clearstatcache();
                        $this->log("Filesize before optimize: " . filesize($this->cacheFileName) . " bytes.");
                    }
                    $res = array();
                    $cmd = $this->jpegOptimizeCmd . " -outfile $this->cacheFileName $this->cacheFileName";
                    exec($cmd, $res);
                    $this->log($cmd);
                    $this->log($res);
                }
                break;

            case 'gif':
                $this->Log("Saving image as GIF to cache.");
                imagegif($this->image, $this->cacheFileName);
                break;

            case 'webp':
                $this->Log("Saving image as WEBP to cache using quality = {$this->quality}.");
                imagewebp($this->image, $this->cacheFileName, $this->quality);
                break;

            case 'png':
            default:
                $this->Log("Saving image as PNG to cache using compression = {$this->compress}.");

                // Turn off alpha blending and set alpha flag
                imagealphablending($this->image, false);
                imagesavealpha($this->image, true);
                imagepng($this->image, $this->cacheFileName, $this->compress);

                // Use external program to process lossy PNG, if defined
                $lossyEnabled = $this->pngLossy === true;
                $lossySoftEnabled = $this->pngLossy === null;
                $lossyActiveEnabled = $this->lossy === true;
                if ($lossyEnabled || ($lossySoftEnabled && $lossyActiveEnabled)) {
                    if ($this->verbose) {
                        clearstatcache();
                        $this->log("Lossy enabled: $lossyEnabled");
                        $this->log("Lossy soft enabled: $lossySoftEnabled");
                        $this->Log("Filesize before lossy optimize: " . filesize($this->cacheFileName) . " bytes.");
                    }
                    $res = array();
                    $cmd = $this->pngLossyCmd . " $this->cacheFileName $this->cacheFileName";
                    exec($cmd, $res);
                    $this->Log($cmd);
                    $this->Log($res);
                }

                // Use external program to filter PNG, if defined
                if ($this->pngFilterCmd) {
                    if ($this->verbose) {
                        clearstatcache();
                        $this->Log("Filesize before filter optimize: " . filesize($this->cacheFileName) . " bytes.");
                    }
                    $res = array();
                    $cmd = $this->pngFilterCmd . " $this->cacheFileName";
                    exec($cmd, $res);
                    $this->Log($cmd);
                    $this->Log($res);
                }

                // Use external program to deflate PNG, if defined
                if ($this->pngDeflateCmd) {
                    if ($this->verbose) {
                        clearstatcache();
                        $this->Log("Filesize before deflate optimize: " . filesize($this->cacheFileName) . " bytes.");
                    }
                    $res = array();
                    $cmd = $this->pngDeflateCmd . " $this->cacheFileName";
                    exec($cmd, $res);
                    $this->Log($cmd);
                    $this->Log($res);
                }
                break;
        }

        if ($this->verbose) {
            clearstatcache();
            $this->log("Saved image to cache.");
            $this->log(" Cached image filesize: " . filesize($this->cacheFileName) . " bytes.");
            $this->log(" imageistruecolor() : " . (imageistruecolor($this->image) ? 'true' : 'false'));
            $this->log(" imagecolorstotal() : " . imagecolorstotal($this->image));
            $this->log(" Number of colors in image = " . $this->ColorsTotal($this->image));
            $index = imagecolortransparent($this->image);
            $this->log(" Detected transparent color = " . ($index > 0 ? implode(", ", imagecolorsforindex($this->image, $index)) : "NONE") . " at index = $index");
        }

        return $this;
    }



    /**
     * Convert image from one colorpsace/color profile to sRGB without
     * color profile.
     *
     * @param string  $src      of image.
     * @param string  $dir      as base directory where images are.
     * @param string  $cache    as base directory where to store images.
     * @param string  $iccFile  filename of colorprofile.
     * @param boolean $useCache or not, default to always use cache.
     *
     * @return string | boolean false if no conversion else the converted
     *                          filename.
     */
    public function convert2sRGBColorSpace($src, $dir, $cache, $iccFile, $useCache = true)
    {
        if ($this->verbose) {
            $this->log("# Converting image to sRGB colorspace.");
        }

        if (!class_exists("Imagick")) {
            $this->log(" Ignoring since Imagemagick is not installed.");
            return false;
        }

        // Prepare
        $this->setSaveFolder($cache)
            ->setSource($src, $dir)
            ->generateFilename(null, false, 'srgb_');

        // Check if the cached version is accurate.
        if ($useCache && is_readable($this->cacheFileName)) {
            $fileTime  = filemtime($this->pathToImage);
            $cacheTime = filemtime($this->cacheFileName);

            if ($fileTime <= $cacheTime) {
                $this->log(" Using cached version: " . $this->cacheFileName);
                return $this->cacheFileName;
            }
        }

        // Only covert if cachedir is writable
        if (is_writable($this->saveFolder)) {
            // Load file and check if conversion is needed
            $image      = new Imagick($this->pathToImage);
            $colorspace = $image->getImageColorspace();
            $this->log(" Current colorspace: " . $colorspace);

            $profiles      = $image->getImageProfiles('*', false);
            $hasICCProfile = (array_search('icc', $profiles) !== false);
            $this->log(" Has ICC color profile: " . ($hasICCProfile ? "YES" : "NO"));

            if ($colorspace != Imagick::COLORSPACE_SRGB || $hasICCProfile) {
                $this->log(" Converting to sRGB.");

                $sRGBicc = file_get_contents($iccFile);
                $image->profileImage('icc', $sRGBicc);

                $image->transformImageColorspace(Imagick::COLORSPACE_SRGB);
                $image->writeImage($this->cacheFileName);
                return $this->cacheFileName;
            }
        }

        return false;
    }



    /**
     * Create a hard link, as an alias, to the cached file.
     *
     * @param string $alias where to store the link,
     *                      filename without extension.
     *
     * @return $this
     */
    public function linkToCacheFile($alias)
    {
        if ($alias === null) {
            $this->log("Ignore creating alias.");
            return $this;
        }

        if (is_readable($alias)) {
            unlink($alias);
        }

        $res = link($this->cacheFileName, $alias);

        if ($res) {
            $this->log("Created an alias as: $alias");
        } else {
            $this->log("Failed to create the alias: $alias");
        }

        return $this;
    }



    /**
     * Add HTTP header for output together with image.
     *
     * @param string $type  the header type such as "Cache-Control"
     * @param string $value the value to use
     *
     * @return void
     */
    public function addHTTPHeader($type, $value)
    {
        $this->HTTPHeader[$type] = $value;
    }



    /**
     * Output image to browser using caching.
     *
     * @param string $file   to read and output, default is to
     *                       use $this->cacheFileName
     * @param string $format set to json to output file as json
     *                       object with details
     *
     * @return void
     */
    public function output($file = null, $format = null)
    {
        if (is_null($file)) {
            $file = $this->cacheFileName;
        }

        if (is_null($format)) {
            $format = $this->outputFormat;
        }

        $this->log("### Output");
        $this->log("Output format is: $format");

        if (!$this->verbose && $format == 'json') {
            header('Content-type: application/json');
            echo $this->json($file);
            exit;
        } elseif ($format == 'ascii') {
            header('Content-type: text/plain');
            echo $this->ascii($file);
            exit;
        }

        $this->log("Outputting image: $file");

        // Get image modification time
        clearstatcache();
        $lastModified = filemtime($file);
        $lastModifiedFormat = "D, d M Y H:i:s";
        $gmdate = gmdate($lastModifiedFormat, $lastModified);

        if (!$this->verbose) {
            $header = "Last-Modified: $gmdate GMT";
            header($header);
            $this->fastTrackCache->addHeader($header);
            $this->fastTrackCache->setLastModified($lastModified);
        }

        foreach ($this->HTTPHeader as $key => $val) {
            $header = "$key: $val";
            header($header);
            $this->fastTrackCache->addHeader($header);
        }

        if (
            isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
            && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified
        ) {

            if ($this->verbose) {
                $this->log("304 not modified");
                $this->verboseOutput();
                exit;
            }

            header("HTTP/1.0 304 Not Modified");
            if (CIMAGE_DEBUG) {
                trace(__CLASS__ . " 304");
            }
        } else {

            $this->loadImageDetails($file);
            $mime = $this->getMimeType();
            $size = filesize($file);

            if ($this->verbose) {
                $this->log("Last-Modified: " . $gmdate . " GMT");
                $this->log("Content-type: " . $mime);
                $this->log("Content-length: " . $size);
                $this->verboseOutput();

                if (is_null($this->verboseFileName)) {
                    exit;
                }
            }

            $header = "Content-type: $mime";
            header($header);
            $this->fastTrackCache->addHeaderOnOutput($header);

            $header = "Content-length: $size";
            header($header);
            $this->fastTrackCache->addHeaderOnOutput($header);

            $this->fastTrackCache->setSource($file);
            $this->fastTrackCache->writeToCache();
            if (CIMAGE_DEBUG) {
                trace(__CLASS__ . " 200");
            }
            readfile($file);
        }

        exit;
    }



    /**
     * Create a JSON object from the image details.
     *
     * @param string $file the file to output.
     *
     * @return string json-encoded representation of the image.
     */
    public function json($file = null)
    {
        $file = $file ? $file : $this->cacheFileName;

        $details = array();

        clearstatcache();

        $details['src']       = $this->imageSrc;
        $lastModified         = filemtime($this->pathToImage);
        $details['srcGmdate'] = gmdate("D, d M Y H:i:s", $lastModified);

        $details['cache']       = basename($this->cacheFileName);
        $lastModified           = filemtime($this->cacheFileName);
        $details['cacheGmdate'] = gmdate("D, d M Y H:i:s", $lastModified);

        $this->load($file);

        $details['filename']    = basename($file);
        $details['mimeType']    = $this->getMimeType($this->fileType);
        $details['width']       = $this->width;
        $details['height']      = $this->height;
        $details['aspectRatio'] = round($this->width / $this->height, 3);
        $details['size']        = filesize($file);
        $details['colors'] = $this->colorsTotal($this->image);
        $details['includedFiles'] = count(get_included_files());
        $details['memoryPeek'] = round(memory_get_peak_usage() / 1024 / 1024, 3) . " MB";
        $details['memoryCurrent'] = round(memory_get_usage() / 1024 / 1024, 3) . " MB";
        $details['memoryLimit'] = ini_get('memory_limit');

        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $details['loadTime'] = (string) round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 3) . "s";
        }

        if ($details['mimeType'] == 'image/png') {
            $details['pngType'] = $this->getPngTypeAsString(null, $file);
        }

        $options = null;
        if (defined("JSON_PRETTY_PRINT") && defined("JSON_UNESCAPED_SLASHES")) {
            $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        }

        return json_encode($details, $options);
    }



    /**
     * Set options for creating ascii version of image.
     *
     * @param array $options empty to use default or set options to change.
     *
     * @return void.
     */
    public function setAsciiOptions($options = array())
    {
        $this->asciiOptions = $options;
    }



    /**
     * Create an ASCII version from the image details.
     *
     * @param string $file the file to output.
     *
     * @return string ASCII representation of the image.
     */
    public function ascii($file = null)
    {
        $file = $file ? $file : $this->cacheFileName;

        $asciiArt = new CAsciiArt();
        $asciiArt->setOptions($this->asciiOptions);
        return $asciiArt->createFromFile($file);
    }



    /**
     * Log an event if verbose mode.
     *
     * @param string $message to log.
     *
     * @return this
     */
    public function log($message)
    {
        if ($this->verbose) {
            $this->log[] = $message;
        }

        return $this;
    }



    /**
     * Do verbose output to a file.
     *
     * @param string $fileName where to write the verbose output.
     *
     * @return void
     */
    public function setVerboseToFile($fileName)
    {
        $this->log("Setting verbose output to file.");
        $this->verboseFileName = $fileName;
    }



    /**
     * Do verbose output and print out the log and the actual images.
     *
     * @return void
     */
    private function verboseOutput()
    {
        $log = null;
        $this->log("### Summary of verbose log");
        $this->log("As JSON: \n" . $this->json());
        $this->log("Memory peak: " . round(memory_get_peak_usage() / 1024 / 1024) . "M");
        $this->log("Memory limit: " . ini_get('memory_limit'));

        $included = get_included_files();
        $this->log("Included files: " . count($included));

        foreach ($this->log as $val) {
            if (is_array($val)) {
                foreach ($val as $val1) {
                    $log .= htmlentities($val1) . '<br/>';
                }
            } else {
                $log .= htmlentities($val) . '<br/>';
            }
        }

        if (!is_null($this->verboseFileName)) {
            file_put_contents(
                $this->verboseFileName,
                str_replace("<br/>", "\n", $log)
            );
        } else {
            echo <<<EOD
<h1>CImage Verbose Output</h1>
<pre>{$log}</pre>
EOD;
        }
    }



    /**
     * Raise error, enables to implement a selection of error methods.
     *
     * @param string $message the error message to display.
     *
     * @return void
     * @throws Exception
     */
    private function raiseError($message)
    {
        throw new Exception($message);
    }
}



/**
 * Deal with the cache directory and cached items.
 *
 */
class CCache
{
    /**
     * Path to the cache directory.
     */
    private $path;



    /**
     * Set the path to the cache dir which must exist.
     *
     * @param string path to the cache dir.
     *
     * @throws Exception when $path is not a directory.
     *
     * @return $this
     */
    public function setDir($path)
    {
        if (!is_dir($path)) {
            throw new Exception("Cachedir is not a directory.");
        }

        $this->path = $path;

        return $this;
    }



    /**
     * Get the path to the cache subdir and try to create it if its not there.
     *
     * @param string $subdir name of subdir
     * @param array  $create default is to try to create the subdir
     *
     * @return string | boolean as real path to the subdir or
     *                          false if it does not exists
     */
    public function getPathToSubdir($subdir, $create = true)
    {
        $path = realpath($this->path . "/" . $subdir);

        if (is_dir($path)) {
            return $path;
        }

        if ($create && is_writable($this->path)) {
            $path = $this->path . "/" . $subdir;

            if (mkdir($path)) {
                return realpath($path);
            }
        }

        return false;
    }



    /**
     * Get status of the cache subdir.
     *
     * @param string $subdir name of subdir
     *
     * @return string with status
     */
    public function getStatusOfSubdir($subdir)
    {
        $path = realpath($this->path . "/" . $subdir);

        $exists = is_dir($path);
        $res  = $exists ? "exists" : "does not exist";

        if ($exists) {
            $res .= is_writable($path) ? ", writable" : ", not writable";
        }

        return $res;
    }



    /**
     * Remove the cache subdir.
     *
     * @param string $subdir name of subdir
     *
     * @return null | boolean true if success else false, null if no operation
     */
    public function removeSubdir($subdir)
    {
        $path = realpath($this->path . "/" . $subdir);

        if (is_dir($path)) {
            return rmdir($path);
        }

        return null;
    }
}



/**
 * Enable a fast track cache with a json representation of the image delivery.
 *
 */
class CFastTrackCache
{
    /**
     * Cache is disabled to start with.
     */
    private $enabled = false;



    /**
     * Path to the cache directory.
     */
    private $path;



    /**
     * Filename of current cache item.
     */
    private $filename;



    /**
     * Container with items to store as cached item.
     */
    private $container;



    /**
     * Enable or disable cache.
     *
     * @param boolean $enable set to true to enable, false to disable
     *
     * @return $this
     */
    public function enable($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }



    /**
     * Set the path to the cache dir which must exist.
     *
     * @param string $path to the cache dir.
     *
     * @throws Exception when $path is not a directory.
     *
     * @return $this
     */
    public function setCacheDir($path)
    {
        if (!is_dir($path)) {
            throw new Exception("Cachedir is not a directory.");
        }

        $this->path = rtrim($path, "/");

        return $this;
    }



    /**
     * Set the filename to store in cache, use the querystring to create that
     * filename.
     *
     * @param array $clear items to clear in $_GET when creating the filename.
     *
     * @return string as filename created.
     */
    public function setFilename($clear)
    {
        $query = $_GET;

        // Remove parts from querystring that should not be part of filename
        foreach ($clear as $value) {
            unset($query[$value]);
        }

        arsort($query);
        $queryAsString = http_build_query($query);

        $this->filename = md5($queryAsString);

        if (CIMAGE_DEBUG) {
            $this->container["query-string"] = $queryAsString;
        }

        return $this->filename;
    }



    /**
     * Add header items.
     *
     * @param string $header add this as header.
     *
     * @return $this
     */
    public function addHeader($header)
    {
        $this->container["header"][] = $header;
        return $this;
    }



    /**
     * Add header items on output, these are not output when 304.
     *
     * @param string $header add this as header.
     *
     * @return $this
     */
    public function addHeaderOnOutput($header)
    {
        $this->container["header-output"][] = $header;
        return $this;
    }



    /**
     * Set path to source image to.
     *
     * @param string $source path to source image file.
     *
     * @return $this
     */
    public function setSource($source)
    {
        $this->container["source"] = $source;
        return $this;
    }



    /**
     * Set last modified of source image, use to check for 304.
     *
     * @param string $lastModified
     *
     * @return $this
     */
    public function setLastModified($lastModified)
    {
        $this->container["last-modified"] = $lastModified;
        return $this;
    }



    /**
     * Get filename of cached item.
     *
     * @return string as filename.
     */
    public function getFilename()
    {
        return $this->path . "/" . $this->filename;
    }



    /**
     * Write current item to cache.
     *
     * @return boolean if cache file was written.
     */
    public function writeToCache()
    {
        if (!$this->enabled) {
            return false;
        }

        if (is_dir($this->path) && is_writable($this->path)) {
            $filename = $this->getFilename();
            return file_put_contents($filename, json_encode($this->container)) !== false;
        }

        return false;
    }



    /**
     * Output current item from cache, if available.
     *
     * @return void
     */
    public function output()
    {
        $filename = $this->getFilename();
        if (!is_readable($filename)) {
            return;
        }

        $item = json_decode(file_get_contents($filename), true);

        if (!is_readable($item["source"])) {
            return;
        }

        foreach ($item["header"] as $value) {
            header($value);
        }

        if (
            isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])
            && strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]) == $item["last-modified"]
        ) {
            header("HTTP/1.0 304 Not Modified");
            if (CIMAGE_DEBUG) {
                trace(__CLASS__ . " 304");
            }
            exit;
        }

        foreach ($item["header-output"] as $value) {
            header($value);
        }

        if (CIMAGE_DEBUG) {
            trace(__CLASS__ . " 200");
        }
        readfile($item["source"]);
        exit;
    }
}



/**
 * Resize and crop images on the fly, store generated images in a cache.
 *
 * @author  Mikael Roos mos@dbwebb.se
 * @example http://dbwebb.se/opensource/cimage
 * @link    https://github.com/mosbth/cimage
 *
 */

/**
 * Custom exception handler.
 */
set_exception_handler(function ($exception) {
    errorPage(
        "<p><b>img.php: Uncaught exception:</b> <p>"
            . $exception->getMessage()
            . "</p><pre>"
            . $exception->getTraceAsString()
            . "</pre>",
        500
    );
});



/**
 * Get configuration options from file, if the file exists, else use $config
 * if its defined or create an empty $config.
 */
$configFile = __DIR__ . '/' . basename(__FILE__, '.php') . '_config.php';

if (is_file($configFile)) {
    $config = require $configFile;
} elseif (!isset($config)) {
    $config = array();
}

// Make CIMAGE_DEBUG false by default, if not already defined
if (!defined("CIMAGE_DEBUG")) {
    define("CIMAGE_DEBUG", false);
}



/**
 * Setup the autoloader, but not when using a bundle.
 */
if (!defined("CIMAGE_BUNDLE")) {
    if (!isset($config["autoloader"])) {
        die("CImage: Missing autoloader.");
    }

    require $config["autoloader"];
}



/**
 * verbose, v - do a verbose dump of what happens
 * vf - do verbose dump to file
 */
$verbose = getDefined(array('verbose', 'v'), true, false);
$verboseFile = getDefined('vf', true, false);
verbose("img.php version = " . CIMAGE_VERSION);



/**
 * status - do a verbose dump of the configuration
 */
$status = getDefined('status', true, false);



/**
 * Set mode as strict, production or development.
 * Default is production environment.
 */
$mode = getConfig('mode', 'production');

// Settings for any mode
set_time_limit(20);
ini_set('gd.jpeg_ignore_warning', 1);

if (!extension_loaded('gd')) {
    errorPage("Extension gd is not loaded.", 500);
}

// Specific settings for each mode
if ($mode == 'strict') {

    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    $verbose = false;
    $status = false;
    $verboseFile = false;
} elseif ($mode == 'production') {

    error_reporting(-1);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    $verbose = false;
    $status = false;
    $verboseFile = false;
} elseif ($mode == 'development') {

    error_reporting(-1);
    ini_set('display_errors', 1);
    ini_set('log_errors', 0);
    $verboseFile = false;
} elseif ($mode == 'test') {

    error_reporting(-1);
    ini_set('display_errors', 1);
    ini_set('log_errors', 0);
} else {
    errorPage("Unknown mode: $mode", 500);
}

verbose("mode = $mode");
verbose("error log = " . ini_get('error_log'));



/**
 * Set default timezone if not set or if its set in the config-file.
 */
$defaultTimezone = getConfig('default_timezone', null);

if ($defaultTimezone) {
    date_default_timezone_set($defaultTimezone);
} elseif (!ini_get('default_timezone')) {
    date_default_timezone_set('UTC');
}



/**
 * Check if passwords are configured, used and match.
 * Options decide themself if they require passwords to be used.
 */
$pwdConfig   = getConfig('password', false);
$pwdAlways   = getConfig('password_always', false);
$pwdType     = getConfig('password_type', 'text');
$pwd         = get(array('password', 'pwd'), null);

// Check if passwords match, if configured to use passwords
$passwordMatch = null;
if ($pwd) {
    switch ($pwdType) {
        case 'md5':
            $passwordMatch = ($pwdConfig === md5($pwd));
            break;
        case 'hash':
            $passwordMatch = password_verify($pwd, $pwdConfig);
            break;
        case 'text':
            $passwordMatch = ($pwdConfig === $pwd);
            break;
        default:
            $passwordMatch = false;
    }
}

if ($pwdAlways && $passwordMatch !== true) {
    errorPage("Password required and does not match or exists.", 403);
}

verbose("password match = $passwordMatch");



/**
 * Prevent hotlinking, leeching, of images by controlling who access them
 * from where.
 *
 */
$allowHotlinking = getConfig('allow_hotlinking', true);
$hotlinkingWhitelist = getConfig('hotlinking_whitelist', array());

$serverName  = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
$referer     = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
$refererHost = parse_url($referer, PHP_URL_HOST);

if (!$allowHotlinking) {
    if ($passwordMatch) {; // Always allow when password match
        verbose("Hotlinking since passwordmatch");
    } elseif ($passwordMatch === false) {
        errorPage("Hotlinking/leeching not allowed when password missmatch.", 403);
    } elseif (!$referer) {
        errorPage("Hotlinking/leeching not allowed and referer is missing.", 403);
    } elseif (strcmp($serverName, $refererHost) == 0) {; // Allow when serverName matches refererHost
        verbose("Hotlinking disallowed but serverName matches refererHost.");
    } elseif (!empty($hotlinkingWhitelist)) {
        $whitelist = new CWhitelist();
        $allowedByWhitelist = $whitelist->check($refererHost, $hotlinkingWhitelist);

        if ($allowedByWhitelist) {
            verbose("Hotlinking/leeching allowed by whitelist.");
        } else {
            errorPage("Hotlinking/leeching not allowed by whitelist. Referer: $referer.", 403);
        }
    } else {
        errorPage("Hotlinking/leeching not allowed.", 403);
    }
}

verbose("allow_hotlinking = $allowHotlinking");
verbose("referer = $referer");
verbose("referer host = $refererHost");



/**
 * Create the class for the image.
 */
$CImage = getConfig('CImage', 'CImage');
$img = new $CImage();
$img->setVerbose($verbose || $verboseFile);



/**
 * Get the cachepath from config.
 */
$CCache = getConfig('CCache', 'CCache');
$cachePath = getConfig('cache_path', __DIR__ . '/../cache/');
$cache = new $CCache();
$cache->setDir($cachePath);



/**
 * no-cache, nc - skip the cached version and process and create a new version in cache.
 */
$useCache = getDefined(array('no-cache', 'nc'), false, true);

verbose("use cache = $useCache");



/**
 * Prepare fast track cache for swriting cache items.
 */
$fastTrackCache = "fasttrack";
$allowFastTrackCache = getConfig('fast_track_allow', false);

$CFastTrackCache = getConfig('CFastTrackCache', 'CFastTrackCache');
$ftc = new $CFastTrackCache();
$ftc->setCacheDir($cache->getPathToSubdir($fastTrackCache))
    ->enable($allowFastTrackCache)
    ->setFilename(array('no-cache', 'nc'));
$img->injectDependency("fastTrackCache", $ftc);



/**
 *  Load and output images from fast track cache, if items are available
 * in cache.
 */
if ($useCache && $allowFastTrackCache) {
    if (CIMAGE_DEBUG) {
        trace("img.php fast track cache enabled and used");
    }
    $ftc->output();
}



/**
 * Allow or disallow remote download of images from other servers.
 * Passwords apply if used.
 *
 */
$allowRemote = getConfig('remote_allow', false);

if ($allowRemote && $passwordMatch !== false) {
    $cacheRemote = $cache->getPathToSubdir("remote");

    $pattern = getConfig('remote_pattern', null);
    $img->setRemoteDownload($allowRemote, $cacheRemote, $pattern);

    $whitelist = getConfig('remote_whitelist', null);
    $img->setRemoteHostWhitelist($whitelist);
}



/**
 * shortcut, sc - extend arguments with a constant value, defined
 * in config-file.
 */
$shortcut       = get(array('shortcut', 'sc'), null);
$shortcutConfig = getConfig('shortcut', array(
    'sepia' => "&f=grayscale&f0=brightness,-10&f1=contrast,-20&f2=colorize,120,60,0,0&sharpen",
));

verbose("shortcut = $shortcut");

if (
    isset($shortcut)
    && isset($shortcutConfig[$shortcut])
) {

    parse_str($shortcutConfig[$shortcut], $get);
    verbose("shortcut-constant = {$shortcutConfig[$shortcut]}");
    $_GET = array_merge($_GET, $get);
}



/**
 * src - the source image file.
 */
$srcImage = urldecode(get('src'))
    or errorPage('Must set src-attribute.', 404);

// Get settings for src-alt as backup image
$srcAltImage = urldecode(get('src-alt', null));
$srcAltConfig = getConfig('src_alt', null);
if (empty($srcAltImage)) {
    $srcAltImage = $srcAltConfig;
}

// Check for valid/invalid characters
$imagePath           = getConfig('image_path', __DIR__ . '/img/');
$imagePathConstraint = getConfig('image_path_constraint', true);
$validFilename       = getConfig('valid_filename', '#^[a-z0-9A-Z-/_ \.:]+$#');

// Source is remote
$remoteSource = false;

// Dummy image feature
$dummyEnabled  = getConfig('dummy_enabled', true);
$dummyFilename = getConfig('dummy_filename', 'dummy');
$dummyImage = false;

preg_match($validFilename, $srcImage)
    or errorPage('Source filename contains invalid characters.', 404);

if ($dummyEnabled && $srcImage === $dummyFilename) {

    // Prepare to create a dummy image and use it as the source image.
    $dummyImage = true;
} elseif ($allowRemote && $img->isRemoteSource($srcImage)) {

    // If source is a remote file, ignore local file checks.
    $remoteSource = true;
} else {

    // Check if file exists on disk or try using src-alt
    $pathToImage = realpath($imagePath . $srcImage);

    if (!is_file($pathToImage) && !empty($srcAltImage)) {
        // Try using the src-alt instead
        $srcImage = $srcAltImage;
        $pathToImage = realpath($imagePath . $srcImage);

        preg_match($validFilename, $srcImage)
            or errorPage('Source (alt) filename contains invalid characters.', 404);

        if ($dummyEnabled && $srcImage === $dummyFilename) {
            // Check if src-alt is the dummy image
            $dummyImage = true;
        }
    }

    if (!$dummyImage) {
        is_file($pathToImage)
            or errorPage(
                'Source image is not a valid file, check the filename and that a
                matching file exists on the filesystem.',
                404
            );
    }
}

if ($imagePathConstraint && !$dummyImage && !$remoteSource) {
    // Check that the image is a file below the directory 'image_path'.
    $imageDir = realpath($imagePath);

    substr_compare($imageDir, $pathToImage, 0, strlen($imageDir)) == 0
        or errorPage(
            'Security constraint: Source image is not below the directory "image_path"
            as specified in the config file img_config.php.',
            404
        );
}

verbose("src = $srcImage");



/**
 * Manage size constants from config file, use constants to replace values
 * for width and height.
 */
$sizeConstant = getConfig('size_constant', function () {

    // Set sizes to map constant to value, easier to use with width or height
    $sizes = array(
        'w1' => 613,
        'w2' => 630,
    );

    // Add grid column width, useful for use as predefined size for width (or height).
    $gridColumnWidth = 30;
    $gridGutterWidth = 10;
    $gridColumns     = 24;

    for ($i = 1; $i <= $gridColumns; $i++) {
        $sizes['c' . $i] = ($gridColumnWidth + $gridGutterWidth) * $i - $gridGutterWidth;
    }

    return $sizes;
});

$sizes = call_user_func($sizeConstant);



/**
 * width, w - set target width, affecting the resulting image width, height and resize options
 */
$newWidth     = get(array('width', 'w'));
$maxWidth     = getConfig('max_width', 2000);

// Check to replace predefined size
if (isset($sizes[$newWidth])) {
    $newWidth = $sizes[$newWidth];
}

// Support width as % of original width
if ($newWidth && $newWidth[strlen($newWidth) - 1] == '%') {
    is_numeric(substr($newWidth, 0, -1))
        or errorPage('Width % not numeric.', 404);
} else {
    is_null($newWidth)
        or ($newWidth > 10 && $newWidth <= $maxWidth)
        or errorPage('Width out of range.', 404);
}

verbose("new width = $newWidth");



/**
 * height, h - set target height, affecting the resulting image width, height and resize options
 */
$newHeight = get(array('height', 'h'));
$maxHeight = getConfig('max_height', 2000);

// Check to replace predefined size
if (isset($sizes[$newHeight])) {
    $newHeight = $sizes[$newHeight];
}

// height
if ($newHeight && $newHeight[strlen($newHeight) - 1] == '%') {
    is_numeric(substr($newHeight, 0, -1))
        or errorPage('Height % out of range.', 404);
} else {
    is_null($newHeight)
        or ($newHeight > 10 && $newHeight <= $maxHeight)
        or errorPage('Height out of range.', 404);
}

verbose("new height = $newHeight");



/**
 * aspect-ratio, ar - affecting the resulting image width, height and resize options
 */
$aspectRatio         = get(array('aspect-ratio', 'ar'));
$aspectRatioConstant = getConfig('aspect_ratio_constant', function () {
    return array(
        '3:1'    => 3 / 1,
        '3:2'    => 3 / 2,
        '4:3'    => 4 / 3,
        '8:5'    => 8 / 5,
        '16:10'  => 16 / 10,
        '16:9'   => 16 / 9,
        'golden' => 1.618,
    );
});

// Check to replace predefined aspect ratio
$aspectRatios = call_user_func($aspectRatioConstant);
$negateAspectRatio = ($aspectRatio && $aspectRatio[0] == '!') ? true : false;
$aspectRatio = $negateAspectRatio ? substr($aspectRatio, 1) : $aspectRatio;

if (isset($aspectRatios[$aspectRatio])) {
    $aspectRatio = $aspectRatios[$aspectRatio];
}

if ($negateAspectRatio) {
    $aspectRatio = 1 / $aspectRatio;
}

is_null($aspectRatio)
    or is_numeric($aspectRatio)
    or errorPage('Aspect ratio out of range', 404);

verbose("aspect ratio = $aspectRatio");



/**
 * crop-to-fit, cf - affecting the resulting image width, height and resize options
 */
$cropToFit = getDefined(array('crop-to-fit', 'cf'), true, false);

verbose("crop to fit = $cropToFit");



/**
 * Set default background color from config file.
 */
$backgroundColor = getConfig('background_color', null);

if ($backgroundColor) {
    $img->setDefaultBackgroundColor($backgroundColor);
    verbose("Using default background_color = $backgroundColor");
}



/**
 * bgColor - Default background color to use
 */
$bgColor = get(array('bgColor', 'bg-color', 'bgc'), null);

verbose("bgColor = $bgColor");



/**
 * Do or do not resample image when resizing.
 */
$resizeStrategy = getDefined(array('no-resample'), true, false);

if ($resizeStrategy) {
    $img->setCopyResizeStrategy($img::RESIZE);
    verbose("Setting = Resize instead of resample");
}




/**
 * fill-to-fit, ff - affecting the resulting image width, height and resize options
 */
$fillToFit = get(array('fill-to-fit', 'ff'), null);

verbose("fill-to-fit = $fillToFit");

if ($fillToFit !== null) {

    if (!empty($fillToFit)) {
        $bgColor   = $fillToFit;
        verbose("fillToFit changed bgColor to = $bgColor");
    }

    $fillToFit = true;
    verbose("fill-to-fit (fixed) = $fillToFit");
}



/**
 * no-ratio, nr, stretch - affecting the resulting image width, height and resize options
 */
$keepRatio = getDefined(array('no-ratio', 'nr', 'stretch'), false, true);

verbose("keep ratio = $keepRatio");



/**
 * crop, c - affecting the resulting image width, height and resize options
 */
$crop = get(array('crop', 'c'));

verbose("crop = $crop");



/**
 * area, a - affecting the resulting image width, height and resize options
 */
$area = get(array('area', 'a'));

verbose("area = $area");



/**
 * skip-original, so - skip the original image and always process a new image
 */
$useOriginal = getDefined(array('skip-original', 'so'), false, true);
$useOriginalDefault = getConfig('skip_original', false);

if ($useOriginalDefault === true) {
    verbose("skip original is default ON");
    $useOriginal = false;
}

verbose("use original = $useOriginal");



/**
 * quality, q - set level of quality for jpeg images
 */
$quality = get(array('quality', 'q'));
$qualityDefault = getConfig('jpg_quality', null);

is_null($quality)
    or ($quality > 0 and $quality <= 100)
    or errorPage('Quality out of range', 404);

if (is_null($quality) && !is_null($qualityDefault)) {
    $quality = $qualityDefault;
}

verbose("quality = $quality");



/**
 * compress, co - what strategy to use when compressing png images
 */
$compress = get(array('compress', 'co'));
$compressDefault = getConfig('png_compression', null);

is_null($compress)
    or ($compress > 0 and $compress <= 9)
    or errorPage('Compress out of range', 404);

if (is_null($compress) && !is_null($compressDefault)) {
    $compress = $compressDefault;
}

verbose("compress = $compress");



/**
 * save-as, sa - what type of image to save
 */
$saveAs = get(array('save-as', 'sa'));

verbose("save as = $saveAs");



/**
 * scale, s - Processing option, scale up or down the image prior actual resize
 */
$scale = get(array('scale', 's'));

is_null($scale)
    or ($scale >= 0 and $scale <= 400)
    or errorPage('Scale out of range', 404);

verbose("scale = $scale");



/**
 * palette, p - Processing option, create a palette version of the image
 */
$palette = getDefined(array('palette', 'p'), true, false);

verbose("palette = $palette");



/**
 * sharpen - Processing option, post filter for sharpen effect
 */
$sharpen = getDefined('sharpen', true, null);

verbose("sharpen = $sharpen");



/**
 * emboss - Processing option, post filter for emboss effect
 */
$emboss = getDefined('emboss', true, null);

verbose("emboss = $emboss");



/**
 * blur - Processing option, post filter for blur effect
 */
$blur = getDefined('blur', true, null);

verbose("blur = $blur");



/**
 * rotateBefore - Rotate the image with an angle, before processing
 */
$rotateBefore = get(array('rotateBefore', 'rotate-before', 'rb'));

is_null($rotateBefore)
    or ($rotateBefore >= -360 and $rotateBefore <= 360)
    or errorPage('RotateBefore out of range', 404);

verbose("rotateBefore = $rotateBefore");



/**
 * rotateAfter - Rotate the image with an angle, before processing
 */
$rotateAfter = get(array('rotateAfter', 'rotate-after', 'ra', 'rotate', 'r'));

is_null($rotateAfter)
    or ($rotateAfter >= -360 and $rotateAfter <= 360)
    or errorPage('RotateBefore out of range', 404);

verbose("rotateAfter = $rotateAfter");



/**
 * autoRotate - Auto rotate based on EXIF information
 */
$autoRotate = getDefined(array('autoRotate', 'auto-rotate', 'aro'), true, false);

verbose("autoRotate = $autoRotate");



/**
 * filter, f, f0-f9 - Processing option, post filter for various effects using imagefilter()
 */
$filters = array();
$filter = get(array('filter', 'f'));
if ($filter) {
    $filters[] = $filter;
}

for ($i = 0; $i < 10; $i++) {
    $filter = get(array("filter{$i}", "f{$i}"));
    if ($filter) {
        $filters[] = $filter;
    }
}

verbose("filters = " . print_r($filters, 1));



/**
 * json -  output the image as a JSON object with details on the image.
 * ascii - output the image as ASCII art.
 */
$outputFormat = getDefined('json', 'json', null);
$outputFormat = getDefined('ascii', 'ascii', $outputFormat);

verbose("outputformat = $outputFormat");

if ($outputFormat == 'ascii') {
    $defaultOptions = getConfig(
        'ascii-options',
        array(
            "characterSet" => 'two',
            "scale" => 14,
            "luminanceStrategy" => 3,
            "customCharacterSet" => null,
        )
    );
    $options = get('ascii');
    $options = explode(',', $options);

    if (isset($options[0]) && !empty($options[0])) {
        $defaultOptions['characterSet'] = $options[0];
    }

    if (isset($options[1]) && !empty($options[1])) {
        $defaultOptions['scale'] = $options[1];
    }

    if (isset($options[2]) && !empty($options[2])) {
        $defaultOptions['luminanceStrategy'] = $options[2];
    }

    if (count($options) > 3) {
        // Last option is custom character string
        unset($options[0]);
        unset($options[1]);
        unset($options[2]);
        $characterString = implode($options);
        $defaultOptions['customCharacterSet'] = $characterString;
    }

    $img->setAsciiOptions($defaultOptions);
}




/**
 * dpr - change to get larger image to easier support larger dpr, such as retina.
 */
$dpr = get(array('ppi', 'dpr', 'device-pixel-ratio'), 1);

verbose("dpr = $dpr");



/**
 * convolve - image convolution as in http://php.net/manual/en/function.imageconvolution.php
 */
$convolve = get('convolve', null);
$convolutionConstant = getConfig('convolution_constant', array());

// Check if the convolve is matching an existing constant
if ($convolve && isset($convolutionConstant)) {
    $img->addConvolveExpressions($convolutionConstant);
    verbose("convolve constant = " . print_r($convolutionConstant, 1));
}

verbose("convolve = " . print_r($convolve, 1));



/**
 * no-upscale, nu - Do not upscale smaller image to larger dimension.
 */
$upscale = getDefined(array('no-upscale', 'nu'), false, true);

verbose("upscale = $upscale");



/**
 * Get details for post processing
 */
$postProcessing = getConfig('postprocessing', array(
    'png_lossy'        => false,
    'png_lossy_cmd'    => '/usr/local/bin/pngquant --force --output',

    'png_filter'        => false,
    'png_filter_cmd'    => '/usr/local/bin/optipng -q',

    'png_deflate'       => false,
    'png_deflate_cmd'   => '/usr/local/bin/pngout -q',

    'jpeg_optimize'     => false,
    'jpeg_optimize_cmd' => '/usr/local/bin/jpegtran -copy none -optimize',
));



/**
 * lossy - Do lossy postprocessing, if available.
 */
$lossy = getDefined(array('lossy'), true, null);

verbose("lossy = $lossy");



/**
 * alias - Save resulting image to another alias name.
 * Password always apply, must be defined.
 */
$alias          = get('alias', null);
$aliasPath      = getConfig('alias_path', null);
$validAliasname = getConfig('valid_aliasname', '#^[a-z0-9A-Z-_]+$#');
$aliasTarget    = null;

if ($alias && $aliasPath && $passwordMatch) {

    $aliasTarget = $aliasPath . $alias;
    $useCache    = false;

    is_writable($aliasPath)
        or errorPage("Directory for alias is not writable.", 403);

    preg_match($validAliasname, $alias)
        or errorPage('Filename for alias contains invalid characters. Do not add extension.', 404);
} elseif ($alias) {
    errorPage('Alias is not enabled in the config file or password not matching.', 403);
}

verbose("alias = $alias");



/**
 * Add cache control HTTP header.
 */
$cacheControl = getConfig('cache_control', null);

if ($cacheControl) {
    verbose("cacheControl = $cacheControl");
    $img->addHTTPHeader("Cache-Control", $cacheControl);
}



/**
 * interlace - Enable configuration for interlaced progressive JPEG images.
 */
$interlaceConfig  = getConfig('interlace', null);
$interlaceValue   = getValue('interlace', null);
$interlaceDefined = getDefined('interlace', true, null);
$interlace = $interlaceValue ?? $interlaceDefined ?? $interlaceConfig;
verbose("interlace (configfile) = ", $interlaceConfig);
verbose("interlace = ", $interlace);



/**
 * Prepare a dummy image and use it as source image.
 */
if ($dummyImage === true) {
    $dummyDir = $cache->getPathToSubdir("dummy");

    $img->setSaveFolder($dummyDir)
        ->setSource($dummyFilename, $dummyDir)
        ->setOptions(
            array(
                'newWidth'  => $newWidth,
                'newHeight' => $newHeight,
                'bgColor'   => $bgColor,
            )
        )
        ->setJpegQuality($quality)
        ->setPngCompression($compress)
        ->createDummyImage()
        ->generateFilename(null, false)
        ->save(null, null, false);

    $srcImage = $img->getTarget();
    $imagePath = null;

    verbose("src (updated) = $srcImage");
}



/**
 * Prepare a sRGB version of the image and use it as source image.
 */
$srgbDefault = getConfig('srgb_default', false);
$srgbColorProfile = getConfig('srgb_colorprofile', __DIR__ . '/../icc/sRGB_IEC61966-2-1_black_scaled.icc');
$srgb = getDefined('srgb', true, null);

if ($srgb || $srgbDefault) {

    $filename = $img->convert2sRGBColorSpace(
        $srcImage,
        $imagePath,
        $cache->getPathToSubdir("srgb"),
        $srgbColorProfile,
        $useCache
    );

    if ($filename) {
        $srcImage = $img->getTarget();
        $imagePath = null;
        verbose("srgb conversion and saved to cache = $srcImage");
    } else {
        verbose("srgb not op");
    }
}



/**
 * Display status
 */
if ($status) {
    $text  = "img.php version = " . CIMAGE_VERSION . "\n";
    $text .= "PHP version = " . PHP_VERSION . "\n";
    $text .= "Running on: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
    $text .= "Allow remote images = $allowRemote\n";

    $res = $cache->getStatusOfSubdir("");
    $text .= "Cache $res\n";

    $res = $cache->getStatusOfSubdir("remote");
    $text .= "Cache remote $res\n";

    $res = $cache->getStatusOfSubdir("dummy");
    $text .= "Cache dummy $res\n";

    $res = $cache->getStatusOfSubdir("srgb");
    $text .= "Cache srgb $res\n";

    $res = $cache->getStatusOfSubdir($fastTrackCache);
    $text .= "Cache fasttrack $res\n";

    $text .= "Alias path writable = " . is_writable($aliasPath) . "\n";

    $no = extension_loaded('exif') ? null : 'NOT';
    $text .= "Extension exif is $no loaded.<br>";

    $no = extension_loaded('curl') ? null : 'NOT';
    $text .= "Extension curl is $no loaded.<br>";

    $no = extension_loaded('imagick') ? null : 'NOT';
    $text .= "Extension imagick is $no loaded.<br>";

    $no = extension_loaded('gd') ? null : 'NOT';
    $text .= "Extension gd is $no loaded.<br>";

    $text .= checkExternalCommand("PNG LOSSY", $postProcessing["png_lossy"], $postProcessing["png_lossy_cmd"]);
    $text .= checkExternalCommand("PNG FILTER", $postProcessing["png_filter"], $postProcessing["png_filter_cmd"]);
    $text .= checkExternalCommand("PNG DEFLATE", $postProcessing["png_deflate"], $postProcessing["png_deflate_cmd"]);
    $text .= checkExternalCommand("JPEG OPTIMIZE", $postProcessing["jpeg_optimize"], $postProcessing["jpeg_optimize_cmd"]);

    if (!$no) {
        $text .= print_r(gd_info(), 1);
    }

    echo <<<EOD
<!doctype html>
<html lang=en>
<meta charset=utf-8>
<title>CImage status</title>
<pre>$text</pre>
EOD;
    exit;
}



/**
 * Log verbose details to file
 */
if ($verboseFile) {
    $img->setVerboseToFile("$cachePath/log.txt");
}



/**
 * Hook after img.php configuration and before processing with CImage
 */
$hookBeforeCImage = getConfig('hook_before_CImage', null);

if (is_callable($hookBeforeCImage)) {
    verbose("hookBeforeCImage activated");

    $allConfig = $hookBeforeCImage($img, array(
        // Options for calculate dimensions
        'newWidth'  => $newWidth,
        'newHeight' => $newHeight,
        'aspectRatio' => $aspectRatio,
        'keepRatio' => $keepRatio,
        'cropToFit' => $cropToFit,
        'fillToFit' => $fillToFit,
        'crop'      => $crop,
        'area'      => $area,
        'upscale'   => $upscale,

        // Pre-processing, before resizing is done
        'scale'        => $scale,
        'rotateBefore' => $rotateBefore,
        'autoRotate'   => $autoRotate,

        // General processing options
        'bgColor'    => $bgColor,

        // Post-processing, after resizing is done
        'palette'   => $palette,
        'filters'   => $filters,
        'sharpen'   => $sharpen,
        'emboss'    => $emboss,
        'blur'      => $blur,
        'convolve'  => $convolve,
        'rotateAfter' => $rotateAfter,
        'interlace' => $interlace,

        // Output format
        'outputFormat' => $outputFormat,
        'dpr'          => $dpr,

        // Other
        'postProcessing' => $postProcessing,
        'lossy' => $lossy,
    ));
    verbose(print_r($allConfig, 1));
    extract($allConfig);
}



/**
 * Display image if verbose mode
 */
if ($verbose) {
    $query = array();
    parse_str($_SERVER['QUERY_STRING'], $query);
    unset($query['verbose']);
    unset($query['v']);
    unset($query['nocache']);
    unset($query['nc']);
    unset($query['json']);
    $url1 = '?' . htmlentities(urldecode(http_build_query($query)));
    $url2 = '?' . urldecode(http_build_query($query));
    echo <<<EOD
<!doctype html>
<html lang=en>
<meta charset=utf-8>
<title>CImage verbose output</title>
<style>body{background-color: #ddd}</style>
<a href=$url1><code>$url1</code></a><br>
<img src='{$url1}' />
<pre id="json"></pre>
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
window.getDetails = function (url, id) {
  $.getJSON(url, function(data) {
    element = document.getElementById(id);
    element.innerHTML = "filename: " + data.filename + "\\nmime type: " + data.mimeType + "\\ncolors: " + data.colors + "\\nsize: " + data.size + "\\nwidth: " + data.width + "\\nheigh: " + data.height + "\\naspect-ratio: " + data.aspectRatio + ( data.pngType ? "\\npng-type: " + data.pngType : '');
  });
}
</script>
<script type="text/javascript">window.getDetails("{$url2}&json", "json")</script>
EOD;
}



/**
 * Load, process and output the image
 */
$img->log("Incoming arguments: " . print_r(verbose(), 1))
    ->setSaveFolder($cachePath)
    ->useCache($useCache)
    ->setSource($srcImage, $imagePath)
    ->setOptions(
        array(
            // Options for calculate dimensions
            'newWidth'  => $newWidth,
            'newHeight' => $newHeight,
            'aspectRatio' => $aspectRatio,
            'keepRatio' => $keepRatio,
            'cropToFit' => $cropToFit,
            'fillToFit' => $fillToFit,
            'crop'      => $crop,
            'area'      => $area,
            'upscale'   => $upscale,

            // Pre-processing, before resizing is done
            'scale'        => $scale,
            'rotateBefore' => $rotateBefore,
            'autoRotate'   => $autoRotate,

            // General processing options
            'bgColor'    => $bgColor,

            // Post-processing, after resizing is done
            'palette'   => $palette,
            'filters'   => $filters,
            'sharpen'   => $sharpen,
            'emboss'    => $emboss,
            'blur'      => $blur,
            'convolve'  => $convolve,
            'rotateAfter' => $rotateAfter,
            'interlace' => $interlace,

            // Output format
            'outputFormat' => $outputFormat,
            'dpr'          => $dpr,

            // Postprocessing using external tools
            'lossy' => $lossy,
        )
    )
    ->loadImageDetails()
    ->initDimensions()
    ->calculateNewWidthAndHeight()
    ->setSaveAsExtension($saveAs)
    ->setJpegQuality($quality)
    ->setPngCompression($compress)
    ->useOriginalIfPossible($useOriginal)
    ->generateFilename($cachePath)
    ->useCacheIfPossible($useCache)
    ->load()
    ->preResize()
    ->resize()
    ->postResize()
    ->setPostProcessingOptions($postProcessing)
    ->save()
    ->linkToCacheFile($aliasTarget)
    ->output();
