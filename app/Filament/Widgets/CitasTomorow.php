<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class CitasTomorow extends BaseWidget
{
    protected int | string | array $columnSpan = 2;


    public function table(Table $table): Table
    {
        return $table
            ->heading('Citas ' . Carbon::now()->addDay()->locale('es')->translatedFormat('l j.n.Y'))
            ->query(
                fn(Builder $query) => Event::query()->whereDate('star', Carbon::today()->addDay())
            )
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Cliente'),
                TextColumn::make('hora')
                    ->alignCenter()
                    ->state(function (Event $record): string {
                        return Carbon::parse($record->star)->format('H:i') . '  ' . Carbon::parse($record->end)->format('H:i');
                    }),
                TextColumn::make('tiempo')
                    ->alignCenter()
                    ->suffix(' ´')
                    ->state(function (Event $record): float {
                        return Carbon::parse($record->star)->floatDiffInMinutes(Carbon::parse($record->end));
                    }),
                TextColumn::make('product.name')
                    ->label('Tratamiento'),
                TextColumn::make('precio')
                    ->money('eur')
                    ->alignEnd(),
                TextColumn::make('extras')
                    ->money('eur')
                    ->alignEnd(),
                TextColumn::make('total')
                    ->money('eur')
                    ->alignEnd(),
            ]);
    }
}
