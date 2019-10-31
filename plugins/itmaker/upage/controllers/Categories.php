<?php namespace Itmaker\Upage\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Categories extends Controller
{
    public $implement = [        
        'Backend\Behaviors\ListController',        
        'Backend\Behaviors\FormController',        
        'Backend\Behaviors\ReorderController',
        'Backend.Behaviors.ImportExportController'
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public $requiredPermissions = [
        'manage_categories' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Itmaker.Upage', 'companies', 'categories');
    }
}
