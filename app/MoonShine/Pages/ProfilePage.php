<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\ProfilePage as MoonShineProfilePage;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

class ProfilePage extends MoonShineProfilePage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        $userFields = array_filter([
            ID::make(),

            Flex::make(array_values(array_filter([
                moonshineConfig()->getUserField('name')
                    ? Text::make(__('moonshine::ui.resource.name'), moonshineConfig()->getUserField('name'))
                        ->required()
                    : null,

                moonshineConfig()->getUserField('username')
                    ? Text::make(__('moonshine::ui.login.username'), moonshineConfig()->getUserField('username'))
                        ->required()
                    : null,
            ])))->itemsAlign('start')->justifyAlign('start'),

            Textarea::make('Author bio', 'bio')
                ->hint('Used on public blog posts when a post does not set its own author profile.'),

            moonshineConfig()->getUserField('avatar')
                ? Image::make(__('moonshine::ui.resource.avatar'), moonshineConfig()->getUserField('avatar'))
                    ->disk(moonshineConfig()->getDisk())
                    ->options(moonshineConfig()->getDiskOptions())
                    ->dir(moonshineConfig()->getUserAvatarsDir())
                    ->removable()
                    ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif'])
                : null,
        ]);

        $userPasswordsFields = moonshineConfig()->getUserField('password') ? [
            Heading::make(__('moonshine::ui.resource.change_password')),

            Password::make(__('moonshine::ui.resource.password'), moonshineConfig()->getUserField('password'))
                ->customAttributes(['autocomplete' => 'new-password'])
                ->eye(),

            PasswordRepeat::make(__('moonshine::ui.resource.repeat_password'), 'password_repeat')
                ->customAttributes(['autocomplete' => 'confirm-password'])
                ->eye(),
        ] : [];

        return [
            Box::make([
                Tabs::make([
                    Tab::make(__('moonshine::ui.resource.main_information'), $userFields),
                    Tab::make(__('moonshine::ui.resource.password'), $userPasswordsFields)->canSee(
                        fn (mixed $item): bool => $userPasswordsFields !== [],
                    ),
                ]),
            ]),
        ];
    }
}
