<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * App\VkAction
 *
 * @property int $id
 * @property string $type
 * @property array|null $params
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|VkAction newModelQuery()
 * @method static Builder|VkAction newQuery()
 * @method static Builder|VkAction query()
 * @method static Builder|VkAction whereCreatedAt($value)
 * @method static Builder|VkAction whereId($value)
 * @method static Builder|VkAction whereParams($value)
 * @method static Builder|VkAction whereType($value)
 * @method static Builder|VkAction whereUpdatedAt($value)
 * @method static Builder|VkAction whereUserId($value)
 * @mixin Eloquent
 */
class VkAction extends Model
{

    const WAIT_DEBTOR = 'wait_debtor';
    const WAIT_DEBT = 'wait_debt';

    protected $fillable = [
        'type',
        'params'
    ];

    protected $casts = [
        'params' => 'array'
    ];

}
