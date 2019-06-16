<?php

namespace Minbaby\SwoftClient;

use ReflectionException;
use Minbaby\SwoftClient\Contract\PacketInterface;
use Minbaby\SwoftClient\Packet\JsonPacket;
use Minbaby\SwoftClient\Packet\AbstractPacket;
use Minbaby\SwoftClient\Exception\RpcException;

class Packet implements PacketInterface
{
    /**
     * Json packet
     */
    const JSON = 'JSON';

    /**
     * Packet type
     *
     * @var string
     */
    private $type = self::JSON;

    /**
     * Packet
     */
    private $packets = [];

    /**
     * @var bool
     */
    private $openEofCheck = true;

    /**
     * @var string
     */
    private $packageEof = "\r\n\r\n";

    /**
     * @var bool
     */
    private $openEofSplit = false;

    /**
     * @var AbstractPacket
     */
    private $packet;

    /**
     * @param Protocol $protocol
     *
     * @return string
     * @throws RpcException
     * @throws ReflectionException
     */
    public function encode(Protocol $protocol): string
    {
        $packet = $this->getPacket();
        return $packet->encode($protocol);
    }

    /**
     * @param string $string
     *
     * @return Protocol
     * @throws RpcException
     * @throws ReflectionException
     */
    public function decode(string $string): Protocol
    {
        $packet = $this->getPacket();
        return $packet->decode($string);
    }

    /**
     * @param mixed    $result
     * @param int|null $code
     * @param string   $message
     * @param null     $data
     *
     * @return string
     * @throws RpcException
     * @throws ReflectionException
     */
    public function encodeResponse($result, int $code = null, string $message = '', $data = null): string
    {
        $packet = $this->getPacket();
        return $packet->encodeResponse($result, $code, $message, $data);
    }

    /**
     * @param string $string
     *
     * @return Response
     * @throws RpcException
     * @throws ReflectionException
     */
    public function decodeResponse(string $string): Response
    {
        $packet = $this->getPacket();
        return $packet->decodeResponse($string);
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function defaultPackets(): array
    {
        return [
            self::JSON => new JsonPacket()
        ];
    }

    /**
     * @return bool
     */
    public function isOpenEofCheck(): bool
    {
        return $this->openEofCheck;
    }

    /**
     * @return string
     */
    public function getPackageEof(): string
    {
        return $this->packageEof;
    }

    /**
     * @return bool
     */
    public function isOpenEofSplit(): bool
    {
        return $this->openEofSplit;
    }

    /**
     * @return PacketInterface
     * @throws RpcException
     * @throws ReflectionException
     */
    private function getPacket(): PacketInterface
    {
        if (!empty($this->packet)) {
            return $this->packet;
        }

        $packets = array_merge($this->defaultPackets(), $this->packets);
        $packet  = $packets[$this->type] ?? null;
        if (empty($packet)) {
            throw new RpcException(
                sprintf('Packet type(%s) is not supported!', $this->type)
            );
        }

        if (!$packet instanceof AbstractPacket) {
            throw new RpcException(
                sprintf('Packet type(%s) is not instanceof PacketInterface!', $this->type)
            );
        }

        $packet->initialize($this);
        $this->packet = $packet;

        return $packet;
    }
}
