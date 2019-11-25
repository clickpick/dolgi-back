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

    const GROUP_TOKEN = 'group';
    const APP_TOKEN = 'app';

    protected $client;
    private $accessToken;

    private const API_VERSION = '5.103';

    public function __construct($tokenType = self::GROUP_TOKEN)
    {
        $this->client = new VKApiClient(self::API_VERSION, 'ru');
        $this->accessToken = config('services.vk.' . $tokenType . '.service_key');
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
     * @param null $fields
     * @return array
     * @throws VKApiException
     * @throws VKClientException
     */
    public function getFriends($vkUserId, $fields = null)
    {
        $request = [
            'user_id' => $vkUserId
        ];

        if ($fields) {
            $request['fields'] = $fields;
        }

        $result = $this->client->friends()->get($this->accessToken, $request);

        return $result['items'] ?? [];
    }

    public function sendMessage(OutgoingMessage $outgoingMessage)
    {

        $this->client->messages()->send($this->accessToken, $outgoingMessage->toVkRequest());

    }
}
