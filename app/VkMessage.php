<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\VkMessage
 *
 * @property int $id
 * @property string|null $message
 * @property array|null $params
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|VkMessage newModelQuery()
 * @method static Builder|VkMessage newQuery()
 * @method static Builder|VkMessage query()
 * @method static Builder|VkMessage whereCreatedAt($value)
 * @method static Builder|VkMessage whereId($value)
 * @method static Builder|VkMessage whereMessage($value)
 * @method static Builder|VkMessage whereParams($value)
 * @method static Builder|VkMessage whereUpdatedAt($value)
 * @method static Builder|VkMessage whereUserId($value)
 * @mixin Eloquent
 */
class VkMessage extends Model
{
    protected $fillable = [
        'message',
        'params'
    ];

    protected $casts = [
        'params' => 'array'
    ];

}
