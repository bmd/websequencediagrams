<?php
namespace WebSequenceDiagrams;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Diagram
 * @package WebSequenceDiagrams
 */
class Diagram
{
    //--------------------
    // Constants

    const WSD_URL = "http://www.websequencediagrams.com/";

    const API_VER = '1';

    //--------------------
    // Request Properties

    /** @var string */
    protected $message;

    /** @var string */
    protected $style;

    /** @var string */
    protected $format;

    /** @var string */
    protected $apiKey;

    //--------------------
    // Http Objects

    /** @var Client */
    protected $guzzle;

    /** @var Response */
    protected $response;

    /**
     * Diagram constructor.
     *
     * @param string $message
     * @param string $style
     * @param string $format
     * @param string|null $apiKey
     */
    public function __construct($message = '', $style = 'default', $format = 'png', $apiKey = null)
    {
        $this->setStyle($style);
        $this->setFormat($format);
        $this->setMessage($message);
        $this->setApiKey($apiKey);
    }

    /**
     * @return Client
     */
    public function getGuzzleClient()
    {
        if (!isset($this->guzzle)) {
            $this->guzzle = new Client(['base_uri' => self::WSD_URL]);
        }

        return $this->guzzle;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param string $style
     * @return self
     */
    public function setStyle($style)
    {
        $validStyles = [
            'default',
            'earth',
            'magazine',
            'modern-blue',
            'mscgen',
            'napkin',
            'omegapple',
            'patent',
            'qsd',
            'rose',
            'roundgreen',
        ];

        if (!in_array($style, $validStyles)) {
            throw new \InvalidArgumentException("Style must be one of: '" . implode("', '", $validStyles) . "'");
        }

        $this->style = $style;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setFormat($format)
    {
        $validFormats = [
            "png",
            "pdf",
            "svg",
        ];

        if (!in_array($format, $validFormats)) {
            throw new \InvalidArgumentException("Format must be one of: '" . implode("', '", $validFormats) . "'");
        }

        $this->format = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setApiKey($key)
    {
        $this->apiKey = $key;

        return $this;
    }

    /**
     * @return Response|ResponseInterface
     */
    public function render()
    {
        $postParams = [
            "apiVersion" => self::API_VER,
            "message" => $this->getMessage(),
            "style" => $this->getStyle(),
            "format" => $this->getFormat(),
        ];

        if (isset($this->apiKey)) {
            $postParams['apiKey'] = $this->apiKey;
        }

        $response = $this->getGuzzleClient()
            ->post('/', ['form_params' => $postParams]);

        return $this->response = $response;
    }

    /**
     * @param string|null $baseFileName
     * @param string $dir
     * @return string The full path of the file
     */
    public function save($dir = '.', $baseFileName = null)
    {
        if (!$this->response) {
            throw new \RuntimeException("You must call Diagram::render() before saving the output image");
        }

        if ($baseFileName) {
            $fullPath = "{$dir}/{$baseFileName}.{$this->format}";
        } else {
            $fullPath = "{$dir}/" . md5($this->message) . ".{$this->format}";
        }

        $json = json_decode($this->response->getBody()->getContents());

        $this->getGuzzleClient()
            ->get($json->img, ['save_to' => $fullPath]);

        return $fullPath;
    }
}
