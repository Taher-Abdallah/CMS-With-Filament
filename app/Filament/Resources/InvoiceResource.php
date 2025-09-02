<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Date;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InvoiceResource\RelationManagers;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 4;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('Created_at', today())->count();
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
            ->schema([
            Forms\Components\Select::make('appointment_id')
                ->label('Appointment Name')
                ->relationship('appointment', 'patient_id')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->patient->name  . ' - ' . $record->doctor->name)
                ->createOptionForm(Appointment::getForm())
                ->required()
                ->searchable()
                ->preload()
                   ,
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('appointment_id')
                ->label('Appointment Details')
                ->description('اسم المريض - اسم الطبيب - الاختصاص')
                ->getStateUsing(function ($record) {
                    return $record->appointment->patient->name  . ' - ' 
                    . $record->appointment->doctor->name . ' - ' . $record->appointment->doctor->specialty;
                })
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->colors([
                    'success' => 'paid',
                    'danger' => 'unpaid',
                ]),
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
                SelectFilter::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ]),
                    SelectFilter::make('specialty')
                    ->label('Specialty')
                    ->relationship('appointment', 'doctor_id')
                ->getOptionLabelFromRecordUsing(fn($record) => $record->doctor->specialty)

                ->options(Doctor::distinct('specialty')->pluck('specialty', 'specialty')),

            Filter::make('date')
                ->form([
                    Forms\Components\DatePicker::make('date')
                        ->label('اختر التاريخ'),
                ])
                ->query(function ($query, array $data) {
                    return $query->when(
                        $data['date'],
                        fn($q, $date) => $q->whereDate('created_at', $date),
                    );
                }),

                

            ])
            ->actions([

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
                Section::make('Invoices Details')->schema([
                    TextEntry::make('appointment_id')
                        ->label('Patient Details')
                    ->getStateUsing(function ($record) {
                        return $record->appointment->patient->name  . ' - '
                            . $record->appointment->doctor->name . ' - ' . $record->appointment->doctor->specialty;
                    })
                    ,

                    TextEntry::make('amount')
                        ->label('Amount')
                        ->numeric(),


                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->colors([
                            'success' => 'paid',
                            'danger' => 'unpaid',
                        ]),


                ])->columns(3)

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),

            // 'view' => Pages\ViewInvoice::route('/record'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
