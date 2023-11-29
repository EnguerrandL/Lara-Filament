<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Create Post')
                ->description('Create a new post')
                ->schema([

                    Toggle::make('published')->required()->columnSpanFull(),
                    TextInput::make('title')->rules('min:3')->required(),
                    TextInput::make('slug')->unique(ignoreRecord:true)->required(),
                  
                    ColorPicker::make('color')->required(),

                    MarkdownEditor::make('content')->columnSpanFull(),
                ])->columnSpan(2)->columns(2),
            Group::make()->schema([
                Section::make('Image')->schema([
                    FileUpload::make('thumbnail')->disk('public')->directory('users-thumbnail'),

                ])->columnSpan(1)->columns(1),
                Section::make('Meta')->schema([
                    TagsInput::make('tags')->required(),
                ]),
            ])

        ])->columns([
            'default' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
