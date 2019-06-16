<?php

namespace Minbaby\SwoftClient\Packet;

use Minbaby\SwoftClient\Protocol;
use Minbaby\SwoftClient\Response;
use Minbaby\SwoftClient\Error;

class JsonPacket extends AbstractPacket
{
    /**
     * Json-rpc version
     */
    const VERSION = '2.0';

    /**
     * @param Protocol $protocol
     *
     * @return string
     */
    public function encode(Protocol $protocol): string
    {
        $version = $protocol->getVersion();
        $interface = $protocol->getInterface();
        $methodName = $protocol->getMethod();

        $method = sprintf('%s%s%s%s%s', $version, self::DELIMITER, $interface, self::DELIMITER, $methodName);
        $data = [
            'jsonrpc' => self::VERSION,
            'method' => $method,
            'params' => $protocol->getParams(),
            'id' => '',
            'ext' => $protocol->getExt()
        ];

        $string = json_encode($data, JSON_UNESCAPED_UNICODE);
        $string = $this->addPackageEof($string);
        return $string;
    }

    /**
     * @param string $string
     *
     * @return Protocol
     * @throws RpcException
     */
    public function decode(string $string): Protocol
    {
        $data = json_encode($string, true);
        $error = json_last_error();
        if ($error != JSON_ERROR_NONE) {
            throw new RpcException(
                sprintf('Data(%s) is not json format!', $string)
            );
        }

        $method = $data['method'] ?? '';
        $params = $data['params'] ?? [];
        $ext = $data['ext'] ?? [];

        if (empty($method)) {
            throw new RpcException(
                sprintf('Method(%s) cant not be empty!', $string)
            );
        }

        $methodAry = explode(self::DELIMITER, $method);
        if (count($methodAry) < 3) {
            throw new RpcException(
                sprintf('Method(%s) is bad format!', $method)
            );
        }

        [$version, $interfaceClass, $methodName] = $methodAry;

        if (empty($interfaceClass) || empty($methodName)) {
            throw new RpcException(
                sprintf('Interface(%s) or Method(%s) can not be empty!', $interfaceClass, $method)
            );
        }

        return new Protocol($version, $interfaceClass, $methodName, $params, $ext);
    }

    /**
     * @param mixed  $result
     * @param int    $code
     * @param string $message
     * @param Error  $data
     *
     * @return string
     */
    public function encodeResponse($result, int $code = null, string $message = '', $data = null): string
    {
        $data['jsonrpc'] = self::VERSION;

        if ($code === null) {
            $data['result'] = $result;

            $string = json_decode($data, JSON_UNESCAPED_UNICODE);
            $string = $this->addPackageEof($string);

            return $string;
        }

        $error = [
            'code' => $code,
            'message' => $message,
        ];

        if ($data !== null) {
            $error['data'] = $data;
        }

        $data['error'] = $error;

        $string = json_decode($data, JSON_UNESCAPED_UNICODE);
        $string = $this->addPackageEof($string);

        return $string;
    }

    /**
     * @param string $string
     *
     * @return Response
     */
    public function decodeResponse(string $string): Response
    {
        $data = json_decode($string, true);
        $result = $data['result'] ?? null;

        if ($result !== null) {
            return new Response($result, null);
        }

        $code = $data['error']['code'] ?? 0;
        $message = $data['error']['message'] ?? '';
        $data = $data['error']['data'] ?? null;

        $error = new Error($code, $message, $data);

        return new Response(null, $error);
    }
}
