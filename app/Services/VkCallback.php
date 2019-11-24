<?php

namespace App\Services;

use App\Events\GotNewMessage;

class VkCallback {

    private $request;

    const CONFIRMATION = 'confirmation';
    const MESSAGE_NEW = 'message_new';
    const MESSAGE_EDIT = 'message_edit';
    const MESSAGE_ALLOW = 'message_allow';
    const MESSAGE_DENY = 'message_deny';

    public function __construct($request)
    {
        $this->request = $request;
    }

    private function getType() {
        return $this->request->type;
    }

    private function getObject() {
        return $this->request->object;
    }

    public function handle() {
        switch ($this->getType()) {
            case self::CONFIRMATION:
                return $this->confirmation();
                break;

            case self::MESSAGE_NEW:
                event(new GotNewMessage($this->getObject()));
                break;

            default:
                abort(422, __('unknown type'));
        }

        return $this->successResponse();
    }

    private function confirmation() {
        return config('services.vk.group.confirmation');
    }

    private function successResponse() {
        return 'ok';
    }
}
