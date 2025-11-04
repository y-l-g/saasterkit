<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $title
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\AppNotificationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AppNotification extends Model
{
    /** @use HasFactory<\Database\Factories\AppNotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('dismissed_at');
    }
}
