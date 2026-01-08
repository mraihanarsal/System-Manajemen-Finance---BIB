<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DebugController extends BaseController
{
    public function schema()
    {
        $db = \Config\Database::connect();
        
        try {
            $query = $db->query("DESCRIBE users");
            $results = $query->getResultArray();
            
            echo "<pre>";
            print_r($results);
            echo "</pre>";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function fix_schema()
    {
        $db = \Config\Database::connect();
        
        try {
            // Check if table exists
            $tables = $db->listTables();
            if (!in_array('users', $tables)) {
                echo "Table users does not exist. recreating...<br>";
                // Create table raw
                $sql = "CREATE TABLE users (
                    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    nama VARCHAR(100),
                    username VARCHAR(100) UNIQUE,
                    password VARCHAR(255),
                    role ENUM('admin','user') DEFAULT 'user',
                    foto VARCHAR(255) DEFAULT 'undraw_profile_2.svg',
                    is_master TINYINT(1) DEFAULT 0,
                    status ENUM('active','inactive') DEFAULT 'active',
                    created_at DATETIME,
                    updated_at DATETIME
                )";
                $db->query($sql);
                echo "Table created.<br>";
            } else {
                echo "Table users exists.<br>";
                // Check columns
                $columns = $db->getFieldNames('users');
                
                if (!in_array('nama', $columns)) {
                    $db->query("ALTER TABLE users ADD COLUMN nama VARCHAR(100) AFTER id");
                    echo "Added column nama.<br>";
                }
                if (!in_array('username', $columns)) {
                    $db->query("ALTER TABLE users ADD COLUMN username VARCHAR(100) UNIQUE AFTER nama");
                    echo "Added column username.<br>";
                }
                if (!in_array('password', $columns)) {
                    $db->query("ALTER TABLE users ADD COLUMN password VARCHAR(255) AFTER username");
                    echo "Added column password.<br>";
                }
                if (!in_array('role', $columns)) {
                    $db->query("ALTER TABLE users ADD COLUMN role ENUM('admin','user') DEFAULT 'user' AFTER password");
                    echo "Added column role.<br>";
                }
                 if (!in_array('foto', $columns)) {
                    $db->query("ALTER TABLE users ADD COLUMN foto VARCHAR(255) DEFAULT 'undraw_profile_2.svg' AFTER role");
                    echo "Added column foto.<br>";
                }
                 if (!in_array('is_master', $columns)) {
                    $db->query("ALTER TABLE users ADD COLUMN is_master TINYINT(1) DEFAULT 0 AFTER foto");
                    echo "Added column is_master.<br>";
                }
                 if (!in_array('status', $columns)) {
                    $db->query("ALTER TABLE users ADD COLUMN status ENUM('active','inactive') DEFAULT 'active' AFTER is_master");
                    echo "Added column status.<br>";
                }
            }
            
            echo "Fix completed. Check /debug/schema again.";
            
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
