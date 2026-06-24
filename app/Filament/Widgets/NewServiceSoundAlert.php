<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class NewServiceSoundAlert extends Widget
{
    protected string $view = 'filament.widgets.new-service-sound-alert';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -10;
}