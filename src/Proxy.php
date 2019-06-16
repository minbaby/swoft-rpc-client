<?php

namespace Minbaby\SwoftClient;

use Minbaby\SwoftClient\Contract\PacketInterface;
use Minbaby\SwoftClient\Exception\RpcException;

class Proxy 
{
    /**
     * @var float
     */
    protected $version;
    private $packet;
    private $class;
    private $ext = [];
    protected $host = "";

    public function __construct(string $host, string $class, array $ext = [], string $version = '1.0')
    {
        $this->host = $host;
        $this->class = $class;
        $this->packet = new Packet();
        $this->ext = $ext;
        $this->version = $version;
    }


    public function __call($name, $arguments)
    {
        $protocol = new Protocol(
            $this->class,
            $name,
            $arguments,
            $this->ext,
            $this->version
        );
        return $this->CallRemoteFuc($protocol, $this->packet);
    }

    /**
     * @param Protocol        $protocol
     * @param PacketInterface $packet
     * @return \Minbaby\SwoftClient\Response
     * @throws Exception
     */
    protected function CallRemoteFuc(Protocol $protocol, PacketInterface $packet)
    {
        $fp = stream_socket_client($this->host, $errno, $errstr);
        if (!$fp) {
            throw new RpcException("stream_socket_client fail errno={$errno} errstr={$errstr}");
        }

        $data = $packet->encode($protocol);
        fwrite($fp, $data);

        $result = '';
        while(!feof($fp)) {
            $tmp = stream_socket_recvfrom($fp, 1024);
            if (empty($tmp)) {
                break;
            }
            $result .= $tmp;
            if (strpos($result, $packet->getPackageEof())) {
                break;
            }
        }

        fclose($fp);
        $res = $packet->decodeResponse($result);
        if ($res->getError()) {
            throw new RpcException($res->getError()->getMessage(), $res->getError()->getCode());
        }

        return $res->getResult();
    }
}