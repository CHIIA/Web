<?php

use think\Route;
Route::rule('','chiia/Index/login');
Route::rule('check','chiia/index/check');
Route::rule('chiia/check','chiia/index/check');


return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
