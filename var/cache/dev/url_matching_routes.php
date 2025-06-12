<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/register' => [[['_route' => 'api_register', '_controller' => 'App\\Controller\\AuthController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/login' => [[['_route' => 'api_login', '_controller' => 'App\\Controller\\AuthController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/scores' => [
            [['_route' => 'score_list', '_controller' => 'App\\Controller\\ScoreController::list'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'score_create', '_controller' => 'App\\Controller\\ScoreController::create'], null, ['POST' => 0], null, false, false, null],
        ],
    ],
    [ // $regexpList
    ],
    [ // $dynamicRoutes
    ],
    null, // $checkCondition
];
