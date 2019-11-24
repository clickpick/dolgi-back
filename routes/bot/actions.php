<?php

use App\VkAction;

return [
    VkAction::WAIT_DEBTOR => \App\Jobs\VkBot\FindDebtor::class,
    VkAction::WAIT_DEBT => \App\Jobs\VkBot\WriteDebt::class
];
