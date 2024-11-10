<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class CitasHoy extends BaseWidget
{
    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->modalWidth(MaxWidth::TwoExtraLarge)
                    ->label('Nueva cita')
                    ->model(Event::class)
                    ->form([
                        Grid::make(2)
                            ->schema([
                                Select::make('customer_id')
                                    ->required()
                                    ->relationship('customer', 'name')
                                    ->label('Cliente')
                                    ->searchable()
                                    ->columnSpanFull(),
                                Select::make('user_id')
                                    ->required()
                                    ->label('Peluquero')
                                    ->relationship('user', 'name'),
                                Select::make('product_id')
                                    ->required()
                                    ->label('Tratamiento')
                                    ->relationship('product', 'name'),
                                DateTimePicker::make('star')
                                    ->required()
                                    ->seconds(false)
                                    ->default(Now()),
                                DateTimePicker::make('end')
                                    ->required()
                                    ->seconds(false)
                                    ->after('star')
                                    ->default(Now()),
                                Textarea::make('observaciones')
                                    ->autosize()
                                    ->columnSpanFull(),
                                TextInput::make('precio')
                                    ->suffix('€')
                                    ->integer(),
                                TextInput::make('extras')
                                    ->suffix('€')
                                    ->integer()
                            ])
                    ])
            ])
            ->heading('Citas ' . Carbon::now()->locale('es')->translatedFormat('l j.n.Y'))
            ->query(
                fn(Builder $query) => Event::query()->whereDate('star', Carbon::today())
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
