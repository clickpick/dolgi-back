<?php

use App\Jobs\VkBot\AcceptDebtor;
use App\Jobs\VkBot\AcceptSyncDebts;
use App\Jobs\VkBot\ClearActions;
use App\Jobs\VkBot\DebtorList;
use App\Jobs\VkBot\RequestPayoff;
use App\Jobs\VkBot\RequestSyncDebts;
use App\Jobs\VkBot\SelectDebtor;
use App\Jobs\VkBot\ShowActiveDebtors;
use App\Jobs\VkBot\ShowDebtorHistory;
use App\Jobs\VkBot\StartAddingDebtor;
use App\Jobs\VkBot\TotalDebtorPayoff;
use App\Services\VkCommand;

return [
    VkCommand::ADD_DEBTOR => StartAddingDebtor::class,
    VkCommand::ACCEPT_DEBTOR => AcceptDebtor::class,
    VkCommand::DEBTOR_LIST => DebtorList::class,
    VkCommand::SELECT_DEBTOR => SelectDebtor::class,
    VkCommand::CANCEL => ClearActions::class,
    VkCommand::SHOW_HISTORY => ShowDebtorHistory::class,
    VkCommand::TOTAL_PAYOFF => TotalDebtorPayoff::class,
    VkCommand::SHOW_ACTIVE_DEBTORS => ShowActiveDebtors::class,
    VkCommand::REQUEST_PAYOFF => RequestPayoff::class,
    VkCommand::REQUEST_CROSS_DEBT => RequestSyncDebts::class,
    VkCommand::ACCEPT_CROSS_DEBT => AcceptSyncDebts::class
];
