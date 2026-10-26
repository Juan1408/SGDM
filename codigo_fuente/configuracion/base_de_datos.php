<?php

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'puerto' => getenv('DB_PORT') ?: '3306',
    'bdnombre' => getenv('DB_NAME') ?: 'ascend',
    'usuario' => getenv('DB_USER') ?: 'root',
    'contrasena' => getenv('DB_PASSWORD') ?: '',
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
];
