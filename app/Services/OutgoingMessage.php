<?php

namespace App\Services;

use App\User;

class OutgoingMessage
{
    private $message;

    /**
     * @var User|null
     */
    private $recipient;
    private $randomId;

    private $model;

    /**
     * @var VkKeyboard|null
     */
    private $vkKeyboard = null;

    public function __construct($message = null)
    {
        $this->message = $message;
    }

    public function setRecipient(User $user) {
        $this->recipient = $user;
    }

    public function setKeyboard(VkKeyboard $vkKeyboard) {
        $this->vkKeyboard = $vkKeyboard;
    }

    public function getRecipient() {
        return $this->recipient;
    }

    private function setRandomId($randomId) {
        $this->randomId = $randomId;
    }

    public function toVkRequest() {
        $request = [
            'random_id' => $this->randomId,
            'peer_id' => $this->recipient->vk_user_id,
            'message' => $this->message
        ];

        if ($this->vkKeyboard) {
            $request = array_merge($request, [
                'keyboard' => json_encode($this->vkKeyboard->toArray(), JSON_UNESCAPED_UNICODE)
            ]);
        }

        return $request;
    }

    public function createModel() {
        $this->model = $this->recipient->vkMessages()->create([
            'message' => $this->message,
            'params' => []
        ]);

        $this->setRandomId($this->model->id);
    }
}
