<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
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
                Forms\Components\DateTimePicker::make('star')
                    ->required()
                    ->seconds(false)
                    ->default(Now()),
                Forms\Components\DateTimePicker::make('end')
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
            ->columns([
                Tables\Columns\TextColumn::make('star')
                    ->dateTime('j.n.Y H:i'),
                Tables\Columns\TextColumn::make('end')
                    ->dateTime('j.n.Y H:i'),
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
