<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RemoveFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:clear {diskName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove user files from specified drive';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $disk_name = $this->argument('diskName');

        $users_path = config('filesystems.disks.' . $disk_name . '.users_path');

        $result = Storage::disk($disk_name)->deleteDirectory($users_path);

        if ($result) {
            $this->info('User files deleted successfully');
        } else {
            $this->error("We're having trouble deleting users files");
        }
    }
}
