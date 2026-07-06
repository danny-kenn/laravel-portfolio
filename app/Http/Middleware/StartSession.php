<?php

namespace App\Http\Middleware;

use Illuminate\Session\Middleware\StartSession as Middleware;

class StartSession extends Middleware
{
    /**
     * The name of the session cookie.
     *
     * @var string
     */
    protected $cookie = 'laravel_session';
}