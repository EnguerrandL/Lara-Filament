<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Tabs::make('Create new Post')->tabs([
                    Tab::make('Tab 1')->icon('heroicon-o-rectangle-stack')->schema([
                        Toggle::make('published')->required()->columnSpanFull(),
                        TextInput::make('title')->rules('min:3')->required(),
                        TextInput::make('slug')->unique(ignoreRecord: true)->required(),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Category'),
                        // ->searchable(),
                        ColorPicker::make('color')->required(),
                    ]),

                    Tab::make('Content')->schema([
                        MarkdownEditor::make('content')->columnSpanFull(),
                    ]),
                    Tab::make('Meta')->schema([
                        Section::make('Image')->schema([
                            FileUpload::make('thumbnail')->disk('public')->directory('users-thumbnail'),

                        ])->columnSpan(1),
                        Section::make('Meta')->schema([
                            TagsInput::make('tags')->required(),
                        ]),
                    ]),
                ]),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')->disk('public')->height(50),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category.name')->searchable()->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('tags'),
                CheckboxColumn::make('published'),
                ColorColumn::make('color'),
                TextColumn::make('created_at')->label('Published on')
                    ->date('D / M / Y')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter::make('Published Posts')->query(
                //     function (Builder $query): Builder{
                //       return  $query->where('published', true); 
                //     }
                // ),

                TernaryFilter::make('published'),
                SelectFilter::make('category_id')
                ->relationship('category', 'name')
                ->label('Category')
                ->searchable()
                ->multiple()
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),

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

    public static function getRelations(): array
    {
        return [
            AuthorsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
