<?php

// Database Connection Module

function getDBConnection() {
    // Development Switch, 
    // $driver = 'postgres'; 

    try {
        // if ($driver === 'postgres') {
        //     $host = 'localhost';
        //     $port = '5432';
        //     $db   = 'quora_db';
        //     $user = 'quora';
        //     $pass = '090090800908';
        //     $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
        // } else {
            $host = '127.0.0.1';
            $db   = 'quora_db';
            $user = 'root';
            $pass = '';
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        // }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dsn, $user, $pass, $options);

    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}