<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Collective\Html\FormFacade;

/**
 * フォームサービスプロバイダー
 * Class FormProvider
 * @package App\Providers
 */
class FormProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //        Form
        FormFacade::component('form_csv_error', 'components.form_csv_error', ['name']);
        FormFacade::component('form_line_error', 'components.form_line_error', ['name']);
        FormFacade::component('form_confirm_script', 'components.form_confirm_script', ['formId', 'type' => 'store', 'ids' => []]);
        FormFacade::component('form_text', 'components.form_text', ['name', 'label', 'isConfirm', 'attr' => []]);
        FormFacade::component('form_textarea', 'components.form_textarea', ['name', 'label', 'isConfirm', 'attr' => []]);
        FormFacade::component('form_select', 'components.form_select', ['name', 'label', 'option', 'isConfirm', 'attr' => []]);
        FormFacade::component('form_checkbox', 'components.form_checkbox', ['name', 'label', 'option', 'isConfirm', 'attr' => []]);
        FormFacade::component('form_radio', 'components.form_radio', ['name', 'label', 'option', 'isConfirm', 'attr' => []]);
        FormFacade::component('form_email', 'components.form_email', ['name', 'label', 'isConfirm', 'attr' => []]);
        FormFacade::component('form_file', 'components.form_file', ['name', 'label', 'isConfirm', 'attr' => []]);
        FormFacade::component('form_password', 'components.form_password', ['name', 'label', 'attr' => []]);
        FormFacade::component('form_error', 'components.form_error', ['name']);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
