<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('star', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('star')
                    ->label('Día')
                    ->alignCenter()
                    ->dateTime('j.n.Y'),
                Tables\Columns\TextColumn::make('hora')
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
