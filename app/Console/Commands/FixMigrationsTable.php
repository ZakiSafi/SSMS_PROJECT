<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixMigrationsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix MySQL tablespace error for migrations table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Attempting to fix migrations table...');
            
            // Step 1: Try to create the table structure first (if it doesn't exist)
            try {
                DB::statement("CREATE TABLE IF NOT EXISTS migrations (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(255) NOT NULL,
                    batch INT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $this->info('Table structure created.');
            } catch (\Exception $e) {
                $this->warn('Could not create table structure: ' . $e->getMessage());
            }
            
            // Step 2: Try to discard tablespace if table exists
            try {
                DB::statement('ALTER TABLE migrations DISCARD TABLESPACE');
                $this->info('Tablespace discarded.');
            } catch (\Exception $e) {
                $this->warn('Could not discard tablespace: ' . $e->getMessage());
            }
            
            // Step 3: Drop the table completely
            try {
                DB::statement('DROP TABLE IF EXISTS migrations');
                $this->info('Migrations table dropped successfully.');
            } catch (\Exception $e) {
                $this->error('Could not drop table: ' . $e->getMessage());
            }
            
            // Step 4: Provide manual instructions for file deletion
            $this->warn('');
            $this->warn('IMPORTANT: You need to manually delete the .ibd file:');
            $this->warn('1. Stop MySQL service in XAMPP Control Panel');
            $this->warn('2. Navigate to: C:\\xampp\\mysql\\data\\ssms_project\\');
            $this->warn('3. Delete the file: migrations.ibd');
            $this->warn('4. Restart MySQL service');
            $this->warn('5. Then run: php artisan migrate');
            $this->warn('');
            $this->info('Alternatively, use phpMyAdmin to drop the migrations table.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
