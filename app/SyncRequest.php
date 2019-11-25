<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\SyncRequest
 *
 * @property int $id
 * @property int $initiator_id
 * @property int $acceptor_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $acceptor
 * @property-read User $initiator
 * @method static Builder|SyncRequest newModelQuery()
 * @method static Builder|SyncRequest newQuery()
 * @method static Builder|SyncRequest query()
 * @method static Builder|SyncRequest whereAcceptorId($value)
 * @method static Builder|SyncRequest whereCreatedAt($value)
 * @method static Builder|SyncRequest whereId($value)
 * @method static Builder|SyncRequest whereInitiatorId($value)
 * @method static Builder|SyncRequest whereUpdatedAt($value)
 * @mixin Eloquent
 */
class SyncRequest extends Model
{
    protected $fillable = [
        'initiator_id',
        'acceptor_id'
    ];

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function acceptor()
    {
        return $this->belongsTo(User::class, 'acceptor_id');
    }
}
