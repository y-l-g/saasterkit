<?php

declare(strict_types=1);

namespace App\Enums\Settings;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
enum ColorEnum: string
{
    case GRAY = 'gray';
    case RED = 'red';
    case ORANGE = 'orange';
    case AMBER = 'amber';
    case YELLOW = 'yellow';
    case LIME = 'lime';
    case GREEN = 'green';
    case EMERALD = 'emerald';
    case TEAL = 'teal';
    case CYAN = 'cyan';
    case SKY = 'sky';
    case BLUE = 'blue';
    case INDIGO = 'indigo';
    case VIOLET = 'violet';
    case PURPLE = 'purple';
    case FUCHSIA = 'fuchsia';
    case PINK = 'pink';
    case ROSE = 'rose';
    case SLATE = 'slate';
    case ZINC = 'zinc';
    case STONE = 'stone';
    case OLD_NEUTRAL = 'old-neutral';
}
