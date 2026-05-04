<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('phone'),
                TextEntry::make('email'),
                TextEntry::make('source')
                    ->label('Источник')
                    ->placeholder('-'),
                IconEntry::make('is_processed')
                    ->label('Обработано')
                    ->boolean(),
                TextEntry::make('message')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->label('Создано'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->label('Обновлено'),
            ]);
    }
}