<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected $default_response = [
        'status' => true,
        'message' => '',
        'data' => null,
    ];

    public function __construct($default_response = null)
    {
        if ($default_response != null) {
            $this->default_response = $default_response;
        }
    }
}
