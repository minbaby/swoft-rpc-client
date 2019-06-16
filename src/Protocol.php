<?php declare(strict_types=1);


namespace Minbaby\SwoftClient;

class Protocol
{
    /**
     * Default version
     */
    const DEFAULT_VERSION = '1.0';

    /**
     * @var string
     */
    private $interface = '';

    /**
     * @var string
     */
    private $method = '';

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var array
     */
    private $ext = [];

    /**
     * @var string
     */
    private $version = self::DEFAULT_VERSION;

    public function __construct($interface, $method, $params, $ext, $version = self::DEFAULT_VERSION)
    {
        $this->interface = $interface;
        $this->method = $method;
        $this->params = $params;
        $this->ext = $ext;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getInterface(): string
    {
        return $this->interface;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getExt(): array
    {
        return $this->ext;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
