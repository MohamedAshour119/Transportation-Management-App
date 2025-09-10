<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class KPIs extends Page
{
    protected string $view = 'filament.pages.k-p-is';

    protected static ?string $navigationLabel = 'KPIs';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';


    public function getHeading(): string
    {
        return '';
    }
}
