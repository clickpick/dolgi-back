<?php

namespace App;

use App\Events\DebtLogSaved;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\DebtLog
 *
 * @property int $id
 * @property float $value
 * @property int $user_id
 * @property int $debtor_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|DebtLog newModelQuery()
 * @method static Builder|DebtLog newQuery()
 * @method static Builder|DebtLog query()
 * @method static Builder|DebtLog whereCreatedAt($value)
 * @method static Builder|DebtLog whereDebtorId($value)
 * @method static Builder|DebtLog whereId($value)
 * @method static Builder|DebtLog whereUpdatedAt($value)
 * @method static Builder|DebtLog whereUserId($value)
 * @method static Builder|DebtLog whereValue($value)
 * @mixin Eloquent
 * @property string $comment
 * @property-read User $debtor
 * @property-read User $user
 * @method static Builder|DebtLog whereComment($value)
 */
class DebtLog extends Model
{
    protected $fillable = [
        'value',
        'debtor_id',
        'comment'
    ];

    protected $dispatchesEvents = [
        'saved' => DebtLogSaved::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function debtor()
    {
        return $this->belongsTo(User::class, 'debtor_id');
    }

    public function getFormattedDebtValue($factor = 1)
    {
        $value = $this->value * $factor;

        $emoji = $value < 0 ? '➖' : '➕';
        return $emoji . number_format(abs($value), 0, '.', ' ');
    }
}
