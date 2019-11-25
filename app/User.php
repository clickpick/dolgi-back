<?php

namespace App;

use App\Events\DebtsSynced;
use App\Events\UserCreated;
use App\Services\OutgoingMessage;
use App\Services\VkClient;
use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Auth\Authorizable;
use Spatie\Regex\Regex;

/**
 * App\User
 *
 * @property int $id
 * @property int $vk_user_id
 * @property bool $notifications_are_enabled
 * @property bool $messages_are_enabled
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $avatar_200
 * @property string|null $bdate
 * @property int $sex
 * @property int|null $utc_offset
 * @property string|null $visited_at
 * @property bool $is_admin
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAvatar200($value)
 * @method static Builder|User whereBdate($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsAdmin($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User whereMessagesAreEnabled($value)
 * @method static Builder|User whereNotificationsAreEnabled($value)
 * @method static Builder|User whereSex($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUtcOffset($value)
 * @method static Builder|User whereVisitedAt($value)
 * @method static Builder|User whereVkUserId($value)
 * @mixin Eloquent
 * @property-read Collection|VkMessage[] $vkMessages
 * @property-read int|null $vk_messages_count
 * @property-read Collection|VkAction[] $vkActions
 * @property-read int|null $vk_actions_count
 * @property-read Collection|User[] $debtors
 * @property-read int|null $debtors_count
 * @property-read Collection|DebtLog[] $debtLogs
 * @property-read int|null $debt_logs_count
 * @property-read Collection|User[] $creditors
 * @property-read int|null $creditors_count
 * @property-read Collection|SyncRequest[] $initedSyncRequests
 * @property-read int|null $inited_sync_requests_count
 * @property-read Collection|SyncRequest[] $receivedSyncRequests
 * @property-read int|null $received_sync_requests_count
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'vk_user_id',
        'utc_offset',
        'notifications_are_enabled',
        'messages_are_enabled',
        'visited_at',
    ];

    protected $dispatchesEvents = [
        'created' => UserCreated::class
    ];

    public function vkMessages()
    {
        return $this->hasMany(VkMessage::class);
    }

    public function vkActions()
    {
        return $this->hasMany(VkAction::class);
    }

    public function debtors()
    {
        return $this->belongsToMany(User::class, 'debtors', 'user_id', 'debtor_id')
            ->withTimestamps()
            ->withPivot(['debt_value', 'is_syncing']);
    }

    public function creditors()
    {
        return $this->belongsToMany(User::class, 'debtors', 'debtor_id', 'user_id')
            ->withTimestamps()
            ->withPivot(['debt_value', 'is_syncing']);
    }

    public function initedSyncRequests()
    {
        return $this->hasMany(SyncRequest::class, 'initiator_id');
    }

    public function receivedSyncRequests()
    {
        return $this->hasMany(SyncRequest::class, 'acceptor_id');
    }

    public function debtLogs()
    {
        return $this->hasMany(DebtLog::class);
    }

    public function fillPersonalInfoFromVk($data = null)
    {
        $data = $data ?? (new VkClient())->getUsers($this->vk_user_id, ['first_name', 'last_name', 'photo_200', 'timezone', 'sex', 'bdate']);

        $this->first_name = $data['first_name'] ?? null;
        $this->last_name = $data['last_name'] ?? null;
        $this->avatar_200 = $data['photo_200'] ?? null;
        $this->sex = $data['sex'] ?? 0;

        if (isset($data['bdate'])) {
            $reYear = Regex::match('/\d{1,2}.\d{1,2}.\d{4}/', $data['bdate']);
            $reDay = Regex::match('/\d{1,2}.\d{1,2}/', $data['bdate']);

            if ($reYear->hasMatch()) {
                $this->bdate = Carbon::parse($data['bdate']);
            } elseif ($reDay->hasMatch()) {

                $date = explode('.', $data['bdate']);

                $bdate = new Carbon();

                $bdate->setYear(1);
                $bdate->setMonth($date[1]);
                $bdate->setDay($date[0]);

                $this->bdate = $bdate;

            } else {
                $this->bdate = null;
            }
        }

        if (isset($data['timezone'])) {
            $this->utc_offset = $data['timezone'] * 60;
        }

        $this->save();
    }

    /**
     * @param $vkId
     * @return User
     */
    public static function getByVkId($vkId): ?self
    {
        if (!$vkId) {
            return null;
        }

        return self::firstOrCreate(['vk_user_id' => $vkId]);
    }

    public function enableMessages()
    {

        if ($this->messages_are_enabled) {
            return;
        }

        $this->messages_are_enabled = true;
        $this->save();
    }

    public function disableMessages()
    {

        if (!$this->messages_are_enabled) {
            return;
        }

        $this->messages_are_enabled = false;
        $this->save();
    }

    public function sendVkMessage(OutgoingMessage $outgoingMessage)
    {

        if (!$this->messages_are_enabled) {
            return;
        }

        $outgoingMessage->setRecipient($this);
        $outgoingMessage->createModel();

        (new VkClient())->sendMessage($outgoingMessage);
    }

    public function clearActions()
    {
        $this->vkActions()->delete();
    }

    public function setAction($type, $params = null)
    {
        $this->clearActions();

        return $this->vkActions()->create([
            'type' => $type,
            'params' => $params
        ]);
    }

    public function hasAction()
    {
        return $this->vkActions()->exists();
    }

    public function getAction(): VkAction
    {
        return $this->vkActions()->first();
    }


    public function addDebt($value, $comment, $debtor)
    {
        $this->debtLogs()->create([
            'value' => $value,
            'comment' => $comment,
            'debtor_id' => $debtor->id
        ]);
    }

    public function getLocalDate(\Carbon\Carbon $date)
    {
        if (!$this->utc_offset) {
            return $date;
        }

        return $date->utcOffset($this->utc_offset);
    }

    public function setUtcOffset($offset)
    {
        $this->utc_offset = $offset;
        $this->save();
    }

    public function totalDebtValue()
    {
        return $this->debtors()->sum('debt_value');
    }

    public function isDebtorSynced(User $debtor) {
        return DB::table('debtors')->where('user_id', $this->id)->where('debtor_id', $debtor->id)->where('is_syncing', true)->exists();
    }

    public function debtValueForDebtor(User $debtor)
    {
        if ($this->isDebtorSynced($debtor)) {
            return $this->debtLogs()->where('debtor_id', $debtor->id)->sum('value') - $debtor->debtLogs()->where('debtor_id', $this->id)->sum('value');
        }

        return $this->debtLogs()->where('debtor_id', $debtor->id)->sum('value');
    }

    public function crossDebtUsers()
    {
        $debtors = $this->debtors;
        $creditors = $this->creditors;

        return $debtors->intersect($creditors);
    }

    public function hasCrossDebtUsers()
    {
        return $this->crossDebtUsers()->isNotEmpty();
    }

    public function isCrossDebt(User $debtor)
    {
        return DB::table('debtors')->where('user_id', $this->id)->where('debtor_id', $debtor->id)->exists()
            && DB::table('debtors')->where('user_id', $debtor->id)->where('debtor_id', $this->id)->exists();
    }

    public function syncWithUser(User $user) {
        DB::table('debtors')->where('user_id', $this->id)->where('debtor_id', $user->id)->update(['is_syncing' => true]);
        DB::table('debtors')->where('user_id', $user->id)->where('debtor_id', $this->id)->update(['is_syncing' => true]);

        $this->receivedSyncRequests()->where('initiator_id', $user->id)->delete();
        $this->initedSyncRequests()->where('acceptor_id', $user->id)->delete();

        event(new DebtsSynced($this, $user));
    }
}
