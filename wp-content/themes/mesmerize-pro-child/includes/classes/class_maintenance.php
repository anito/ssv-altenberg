<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_maintenance
 *
 * @author axel
 */
class class_maintenance {

    private $data = array();
    private $render = FALSE;

    public function __construct($template) {
        try {
            $file = ROOT . '/templates/' . strtolower($template) . '.php';

            if (file_exists($file)) {
                $this->render = $file;
            } else {
                throw new customException('Template ' . $template . ' not found!');
            }
        } catch (customException $e) {
            echo $e->errorMessage();
        }
    }

    public function assign($variable, $value) {
        $this->data[$variable] = $value;
    }

    public function __destruct() {
        extract($this->data);
        include($this->render);
    }

}
