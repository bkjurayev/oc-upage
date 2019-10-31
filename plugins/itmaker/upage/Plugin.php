<?php namespace Itmaker\Upage;

use Event;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }
    
    public function registerMailTemplates()
    {
        return [
            'itmaker.upage::mail.activate'
        ];
    }
    
    public function boot() {
      \Rainlab\User\Models\User::extend(function($model){
        $model->addFillable([
          'phone'
        ]);
      });

      // now your actual code for extending fields
      \RainLab\User\Controllers\Users::extendFormFields(function($form, $model, $context){
            
            if (!$model instanceof \RainLab\User\Models\User)
                return;

            if (!$model->exists)
                return;

            $form->addTabFields([
                'phone' => [
                    'tab' => 'rainlab.user::lang.user.account',
                    'type'  => 'text',
                    'label' => 'Телефон',
                ]                
            ]);
        });

        // Extend all backend list usage
        Event::listen('backend.list.extendColumns', function($widget) {

            // Only for the User controller
            if (!$widget->getController() instanceof \RainLab\User\Controllers\Users) {
                return;
            }

            // Only for the User model
            if (!$widget->model instanceof \RainLab\User\Models\User) {
                return;
            }

            $widget->addColumns([
                'phone' => [
                    'label' => 'Phone'
                ]                
            ]);

            $widget->addColumns([
                'id' => [
                    'label' => 'ID'
                ]
            ]);
        });
        Event::listen('rainlab.user.register', function($user, $data) {
            $code = implode('!', [$user->id, $user->getActivationCode()]);
    
            $link = $this->makeActivationUrl($code);
    
            $data = [
                'name' => $user->name,
                'link' => $link,
                'code' => $code
            ];
    
            Mail::send('rainlab.user::mail.activate', $data, function($message) use ($user) {
                $message->to($user->email, $user->name);
            });
            
        });
        

        
    }
}
