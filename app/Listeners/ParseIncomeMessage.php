<?php

namespace App\Listeners;

use App\Events\GotNewMessage;
use App\Jobs\VkBot\ClearActions;
use App\Jobs\VkBot\SetTimezone;
use App\Jobs\VkBot\Start;
use App\Services\IncomeMessage;
use App\Services\OutgoingMessage;
use App\Services\VkCommand;
use App\Services\VkKeyboard;
use App\Services\VkTextButton;
use App\User;
use App\VkAction;

class ParseIncomeMessage
{

    /**
     * @var IncomeMessage
     */
    private $incomeMessage;

    private $actionRoutes = [];
    private $commandRoutes = [];

    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        $this->actionRoutes = $this->mapActions();
        $this->commandRoutes = $this->mapCommands();
    }

    private function mapActions()
    {
        return require base_path('routes/bot/actions.php');
    }

    private function mapCommands()
    {
        return require base_path('routes/bot/commands.php');
    }

    /**
     * Handle the event.
     *
     * @param GotNewMessage $event
     * @return void
     */
    public function handle(GotNewMessage $event)
    {
        $this->incomeMessage = $event->incomeMessage;

        if ($this->incomeMessage->hasGeo()) {
            $this->dispatch(SetTimezone::class);
            return;
        }

        if ($this->incomeMessage->hasCommand()) {
            $this->parseCommands();
            return;
        }

        if ($this->incomeMessage->getUser()->hasAction()) {
            $this->parseActions();
            return;
        }

        $this->fallbackAnswer();
    }

    private function fallbackAnswer()
    {
        $this->dispatch(new Start($this->incomeMessage));
    }

    private function parseCommands()
    {
        $command = $this->incomeMessage->getCommand();

        $this->dispatch($this->commandRoutes[$command->getType()]);
    }

    private function parseActions()
    {
        if ($this->incomeMessage->hasCommand() && $this->incomeMessage->getCommand()->getType() === VkCommand::CANCEL) {
            $this->dispatch(new ClearActions($this->incomeMessage));
            return;
        }

        $user = $this->incomeMessage->getUser();
        $actionType = $user->getAction()->type;

        $this->dispatch($this->actionRoutes[$actionType]);
    }


    private function dispatch($jobName)
    {
        dispatch(new $jobName($this->incomeMessage));
    }
}
