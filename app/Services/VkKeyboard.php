<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Collection;

class VkKeyboard
{

    private $inline = false;
    private $oneTime = false;
    private $buttons = [];

    private $rows = 1;

    public function addButton(VkButton $button)
    {
        $this->buttons[] = $button;
    }

    public function setRows(int $num)
    {
        $this->rows = $num;
    }

    public function toArray()
    {

        $buttons = (new Collection($this->buttons))->map(function (VkButton $vkButton) {
            return $vkButton->toArray();
        });

        return [
            'inline' => $this->inline,
            'one_time' => $this->oneTime,
            'buttons' => $buttons->chunk($this->rows)->toArray()
        ];
    }

    public function setOneTime(bool $value)
    {
        $this->oneTime = $value;
    }

    public function setInline(bool $value)
    {
        $this->inline = $value;
    }


    public static function cancel() {
        $keyboard = new self();
        $keyboard->addButton(VkTextButton::cancel());

        return $keyboard;
    }

    public static function starting(?User $user = null) {
        $debtorsBtn = new VkTextButton('Мои должники');
        $debtorsBtn->setCommand(new VkCommand(VkCommand::DEBTOR_LIST));

        $addDebtorBtn = new VkTextButton("Добавить должника");
        $addDebtorBtn->setCommand(new VkCommand(VkCommand::ADD_DEBTOR));

        $keyboard = new self();

        $keyboard->addButton($debtorsBtn);
        $keyboard->addButton($addDebtorBtn);

        if ($user && !$user->utc_offset) {
            $keyboard->addButton(new VkLocationButton());
        }

        return $keyboard;
    }
}
