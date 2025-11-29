<?php

if (!function_exists('view')) {
    function view($view, $data = []) {
        // En este proyecto usamos Core para las vistas
        return \Core\Response::view($view, $data);
    }
}

if (!function_exists('redirect')) {
    function redirect($to = null) {
        if ($to) {
            return \Core\Response::redirect($to);
        }
        return new RedirectHelper();
    }
}

if (!function_exists('back')) {
    function back() {
        return \Core\Response::back();
    }
}

class RedirectHelper {
    private $url;
    private $data = [];
    private $errors = [];
    
    public function to($url) {
        $this->url = $url;
        return $this;
    }
    
    public function with($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }
    
    public function withErrors($errors) {
        $this->errors = $errors;
        return $this;
    }
    
    public function withInput($input = []) {
        // Guardar input en sesión
        if (!empty($input)) {
            $_SESSION['_old_input'] = $input;
        }
        return $this;
    }
    
    public function intended($default = '/') {
        $intended = $_SESSION['url.intended'] ?? $default;
        unset($_SESSION['url.intended']);
        return \Core\Response::redirect($intended);
    }
    
    public function __destruct() {
        if ($this->url) {
            // Guardar datos en sesión
            if (!empty($this->data)) {
                foreach ($this->data as $key => $value) {
                    $_SESSION['flash'][$key] = $value;
                }
            }
            if (!empty($this->errors)) {
                $_SESSION['errors'] = $this->errors;
            }
            \Core\Response::redirect($this->url);
        }
    }
}

class BackHelper {
    private $errors = [];
    private $input = [];
    private $data = [];
    
    public function withErrors($errors) {
        $this->errors = $errors;
        return $this;
    }
    
    public function withInput($input = []) {
        $this->input = $input;
        return $this;
    }
    
    public function with($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }
    
    public function __destruct() {
        // Guardar en sesión
        if (!empty($this->errors)) {
            $_SESSION['errors'] = $this->errors;
        }
        if (!empty($this->input)) {
            $_SESSION['_old_input'] = $this->input;
        }
        if (!empty($this->data)) {
            foreach ($this->data as $key => $value) {
                $_SESSION['flash'][$key] = $value;
            }
        }
        \Core\Response::back();
    }
}