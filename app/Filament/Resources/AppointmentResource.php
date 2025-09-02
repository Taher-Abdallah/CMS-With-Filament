<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Tables\Table;
use PhpParser\Comment\Doc;
use App\Models\Appointment;
use App\Enums\AppointmentStatus;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use App\Enums\AppointmentStatusColor;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\AppointmentResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Invoice;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?int $navigationSort = 3;
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('date', today())->count();  
      }

    public static function getNavigationBadgeColor(): ?string
    {
        if (static::getNavigationBadge() > 0) {
            return 'success';
        }
        return 'danger';
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema(Appointment::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->numeric()
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                ->searchable()

                ->sortable(),
                Tables\Columns\TextColumn::make('time')
                ->time('h:i A'),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ,
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
            Filter::make('date')
                ->form([
                    Forms\Components\DatePicker::make('date')
                        ->label('اختر التاريخ'),
                ])
                ->query(function ($query, array $data) {
                    return $query->when(
                        $data['date'],
                        fn($q, $date) => $q->whereDate('date', $date),
                    );
                }),
            SelectFilter::make('status')
                ->label('status')
                ->options(AppointmentStatus::class)
                ->multiple()
                ->searchable()
                ->preload(),

            Filter::make('am')
                ->label('AM')
                ->query(fn($query) => $query->whereRaw('HOUR(time) < 12')),

            Filter::make('pm')
                ->label('PM')
                ->query(fn($query) => $query->whereRaw('HOUR(time) >= 12')),

        ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

        ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Appointment Details')->schema([
                    TextEntry::make('patient.name')
                        ->label('Patient Name')
                    ->icon('heroicon-o-user'),

                    TextEntry::make('doctor')
                    ->label('Doctor Name')
                    ->getStateUsing(function ($record) {
                        return $record->doctor->name .' — '. $record->doctor->specialty;
                    })
                        ,

                    TextEntry::make('date')
                        ->label('Date')
                        ->dateTime('d/m/Y'),
                    TextEntry::make('time')
                        ->label('Time')
                      ->dateTime('h:i A')
                ,
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge(),


                ])->columns(5)

            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
