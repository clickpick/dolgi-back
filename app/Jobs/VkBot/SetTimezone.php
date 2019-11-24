<?php

namespace App\Jobs\VkBot;

use App\Services\OutgoingMessage;
use App\Services\VkKeyboard;
use GeoNames\Client;

class SetTimezone extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $client = new Client('ezavalishin');

        try {
            $response = $client->timezone([
                'lat' => $this->incomeMessage->getLocation()['latitude'],
                'lng' => $this->incomeMessage->getLocation()['longitude']
            ]);

            $this->user->setUtcOffset($response->gmtOffset * 60);

            $message = new OutgoingMessage('Таймзона установлена!');
            $message->setKeyboard(VkKeyboard::starting());

            $this->user->sendVkMessage($message);
        } catch (\Exception $e) {
            $message = new OutgoingMessage('Не удалось определить твою таймзону, попробуй позже');
            $message->setKeyboard(VkKeyboard::starting());
            $this->user->sendVkMessage($message);
        }

    }
}
