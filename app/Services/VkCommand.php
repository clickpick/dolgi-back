<?php

namespace App\Services;

class VkCommand {

    const ADD_DEBTOR = "add_debtor";
    const CANCEL = "cancel";
    const ACCEPT_DEBTOR = "accept_debtor";
    const DEBTOR_LIST = 'debtor_list';
    const SELECT_DEBTOR = 'select_debtor';
    const SHOW_HISTORY = 'show_history';
    const TOTAL_PAYOFF = 'total_payoff';
    const SHOW_ACTIVE_DEBTORS = 'show_active_debtors';

    private $type;
    private $params;

    public function __construct(string $type, array $params = [])
    {
        $this->type = $type;
        $this->params = $params;
    }

    public function getType() {
        return $this->type;
    }

    public function getParams() {
        return $this->params;
    }

    public function toPayload() {
        return [
            'type' => $this->getType(),
            'params' => $this->getParams()
        ];
    }
}
