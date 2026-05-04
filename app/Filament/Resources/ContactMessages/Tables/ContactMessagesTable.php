<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('source')
                    ->label('Источник')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'callback' => 'Заказать звонок',
                        'consultation' => 'Консультация',
                        'realty_hub' => 'Оставить заявку',
                        'contacts' => 'Контакты',
                        'ai_chat' => 'AI-чат',
                        default => $state ?: 'Не указан',
                    }),
                TextColumn::make('message')
                    ->label('Сообщение')
                    ->limit(70)
                    ->tooltip(fn ($record): string => (string) $record->message),
                IconColumn::make('is_processed')
                    ->label('Обработано')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label('Источник')
                    ->options([
                        'callback' => 'Заказать звонок',
                        'consultation' => 'Консультация',
                        'realty_hub' => 'Оставить заявку',
                        'contacts' => 'Контакты',
                        'ai_chat' => 'AI-чат',
                    ]),
                SelectFilter::make('is_processed')
                    ->label('Статус')
                    ->options([
                        '1' => 'Обработано',
                        '0' => 'Не обработано',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}