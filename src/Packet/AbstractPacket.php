<?php

namespace Minbaby\SwoftClient\Packet;

use Minbaby\SwoftClient\Contract\PacketInterface;
use Minbaby\SwoftClient\Packet;

abstract class AbstractPacket implements PacketInterface
{
    /**
     * delimiter
     */
    const DELIMITER = '::';

    /**
     * @var Packet
     */
    protected $packet;

    /**
     * @param Packet $packet
     */
    public function initialize(Packet $packet)
    {
        $this->packet = $packet;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function addPackageEof(string $string): string
    {
        // Fix mock server null
        if (empty($this->packet)) {
            return $string;
        }

        if ($this->packet->isOpenEofCheck() || $this->packet->isOpenEofSplit()) {
            $string .= $this->packet->getPackageEof();
        }

        return $string;
    }
}
