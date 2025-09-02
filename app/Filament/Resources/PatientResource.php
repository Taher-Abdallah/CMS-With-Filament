<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PatientResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Filament\Resources\PatientResource\Widgets\PatientsCount;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?int $navigationSort = 2;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Patient::getForm()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('City')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            SelectFilter::make('gender')
                ->label('Gender')
                ->options([
                    'male' => 'Male',
                    'female' => 'Female',
                ]),
                SelectFilter::make('Country')
                ->label('Country')
                ->options(
                    Patient::query()
                        ->select('Country')
                        ->distinct()
                        ->pluck('Country', 'Country')
                ),

            SelectFilter::make('state')
                ->label('State')
                ->options(

                     Patient::query()
                        ->select('state')
                        ->distinct()
                        ->pluck('state', 'state')
                )
                ,
                            Filter::make('age_above_30')
                ->label('Age > 30')
                ->query(fn($query) => $query->where('age', '>', 30)),

            Filter::make('age_below_25')
                ->label('Age < 30')
                ->query(fn($query) => $query->where('age', '<', 30)),
              
    
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            Tables\Actions\ViewAction::make(),

        ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Personal Details')->schema([
                    TextEntry::make('name')
                        ->label('Doctor Name')
                        ->icon('heroicon-o-user'),

                    TextEntry::make('age')
                        ->label('Age')
                        ->copyable(),

                    TextEntry::make('gender')
                        ->label('Gender')
                        ->copyable(),

                    TextEntry::make('phone')
                        ->label('Phone Number'),
                ])->columns(2) ,

                Section::make('Address Information')->schema([
                    TextEntry::make('Country')
                        ->label('Country')
                        ->copyable(),

                    TextEntry::make('state')
                        ->label('State')
                        ->copyable(),

                    TextEntry::make('City')
                        ->label('City')
                        ->copyable(),
                ])->columns(3)

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // public static function getWidgets(): array
    // {
    //     return [
    //         PatientsCount::class,
    //     ];
    // }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'view' => Pages\ViewPatient::route('/{record}'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
