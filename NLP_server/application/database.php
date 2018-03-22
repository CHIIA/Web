<?php

return [
    // database type
    'type'            => '\think\mongo\connection',
    // database address
    'hostname'        => '127.0.0.1',
    // database name
    'database'        => '',
    // username
    'username'        => 'root',
    // password
    'password'        => '',
    // port
    'hostport'        => '',
    // dsn connection
    'dsn'             => '',
    // database connection parameters
    'params'          => [],
    // database charset
    'charset'         => 'utf8',
    // database prefix
    'prefix'          => '',
    // database debug mode
    'debug'           => true,
    // database deploy mode, 0 centralization, 1 distribution
    'deploy'          => 0,
    // read and write separate, (for 0)
    'rw_separate'     => false,
    // master num after separate
    'master_num'      => 1,
    // default slave-server number
    'slave_no'        => '',
    // fields strict check examine
    'fields_strict'   => true,
    // result set type
    'resultset_type'  => 'array',
    // auto write timestamp
    'auto_timestamp'  => false,
    // default datetime format
    'datetime_format' => 'Y-m-d H:i:s',
    // sql explain
    'sql_explain'     => false,
];
