<?php

namespace App\Services;

use App\Jobs\DisableNotificationForVkUser;
use GuzzleHttp\Client;
use Intervention\Image\Image;
use VK\Client\VKApiClient;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

class VkClient
{
    protected $client;
    private $accessToken;

    private const API_VERSION = '5.103';

    public function __construct()
    {
        $this->client = new VKApiClient(self::API_VERSION, 'ru');
        $this->accessToken = config('services.vk.group.service_key');
    }

    public function getUsers($ids, array $fields)
    {

        $isFew = is_array($ids);

        $response = $this->client->users()->get($this->accessToken, [
            'user_ids' => $isFew ? $ids : [$ids],
            'fields' => $fields,
        ]);

        return $isFew ? $response : $response[0];
    }

    /**
     * @param $vkUserId
     * @return array
     * @throws VKApiException
     * @throws VKClientException
     */
    public function getFriends($vkUserId)
    {
        $result = $this->client->friends()->get($this->accessToken, [
            'user_id' => $vkUserId
        ]);

        return $result['items'] ?? [];
    }

    public function sendMessage(OutgoingMessage $outgoingMessage)
    {

        $this->client->messages()->send($this->accessToken, $outgoingMessage->toVkRequest());

    }
}
