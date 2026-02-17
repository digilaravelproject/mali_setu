<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databaseName = $this->argument('name') ?: Config::get('database.connections.mysql.database');
        
        $connection = Config::get('database.connections.mysql');
        $connection['database'] = null;
        
        Config::set('database.connections.mysql_temp', $connection);
        
        try {
            DB::connection('mysql_temp')->statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
            $this->info("Database '{$databaseName}' created successfully!");
        } catch (\Exception $e) {
            $this->error("Failed to create database: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}