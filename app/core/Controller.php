<?php
class Controller
{
    public function view($view, $data = [])
    {   
        $data['current_controller_name'] = get_class($this);
        // Check if the view file exists
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }
    public function model($model)
    {
        // Check if the model file exists
        if (file_exists('../app/models/' . $model . '.php')) {
            require_once '../app/models/' . $model . '.php';
            return new $model; // Return an instance of the model
        } else {
            die('Model does not exist');
        }
    }
}