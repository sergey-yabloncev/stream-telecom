<?php

namespace Yabloncev\StreamTelecom;

use GuzzleHttp\Client;

class StreamTelecom
{
    /**
     * base url
     *
     * @var string
     */
    protected $server = 'http://gateway.api.sc/rest/';

    /**
     * GuzzleHttp
     *
     * @var object
     */
    protected $client;

    /**
     * session id
     *
     * @var string
     */
    protected $sessionId;

    /**
     * source address
     *
     * @var string
     */
    protected $sourceAddress;

    /**
     * StreamTelecom constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->server,
            'headers' => ['Content-type' => 'application/x-www-form-urlencoded'],
        ]);

        $this->sessionId = $this->getSessionId();

        $this->sourceAddress = config('stream_telecom.source');
    }

    /**
     * send data
     *
     * @param string $path relative path
     * @param array $data
     *
     * @return string
     */
    protected function sendData($path, $data)
    {
        $response = $this->client->post($path, ['form_params' => $data])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * clear phone number
     *
     * @param array $phones phones numbers
     *
     * @return string
     */
    protected function clearPhone(array $phones): string
    {
        foreach ($phones as &$v) {
            $v = preg_replace('#[^\d]#', '', $v);
        }

        return implode(',', $phones);
    }

    /**
     * get session id
     *
     * @return string
     */
    protected function getSessionId()
    {
        return $this->sendData('Session/session.php', [
            'login' => config('stream_telecom.login'),
            'password' => config('stream_telecom.password'),
        ]);
    }

    /**
     * get sms status
     *
     * @param $messageId
     *
     * @return string
     */
    public function getState($messageId)
    {
        return $this->sendData('State/state.php', [
            'messageId' => $messageId,
            'sessionId' => $this->sessionId,
        ]);
    }

    /**
     * send sms
     *
     * @param int $phone phone number (format 79111234567)
     * @param string $message message
     *
     * @return string
     */
    public function sendSms($phone, $message)
    {
        return $this->sendData('Send/SendSms/', [
            'sessionId' => $this->sessionId,
            'sourceAddress' => $this->sourceAddress,
            'destinationAddress' => $this->clearPhone([$phone]),
            'data' => $message,
        ]);
    }

    /**
     * The function of sending messages to multiple recipients
     *
     * @param array $phones numbers [num1, num2, ..]
     * @param string $message message
     *
     * @return string
     */
    public function sendBulk(array $phones, string $message)
    {
        return $this->sendData('Send/SendBulk/', [
            'sessionId' => $this->sessionId,
            'sourceAddress' => $this->sourceAddress,
            'destinationAddresses' => $this->clearPhone($phones),
            'data' => $message,
        ]);
    }

    /**
     * Batch Message Feature
     *
     * @param array $phone_data texts and numbers
     *
     * @return string
     */
    public function SendBulkPacket(array $phone_data)
    {
        return $this->sendData('Send/SendBulkPacket/', [
            'sessionId' => $this->sessionId,
            'sourceAddress' => $this->sourceAddress,
            'phone_data' => ['sms' => $phone_data],
        ]);
    }
}
