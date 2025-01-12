<?php

namespace Utyemma\LaraNotice\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Utyemma\LaraNotice\Models\Mailable;

class DeleteMailable extends Command
{
    protected $signature = 'mailable:delete
                        {--all : Delete all mailable classes}
                        {--class= : Specify a single mailable class to delete}
                        {--preserve= : Keep either database records or files (database|files)}';

    protected $description = 'Delete mailable classes from filesystem and/or database';

    private $baseMailablePath;

    public function __construct()
    {
        parent::__construct();
        $this->baseMailablePath = app_path('Mailables');
    }

    public function handle()
    {
        if (!$this->option('all') && !$this->option('class')) {
            $this->error('Please specify either --all or --class option');
            return 1;
        }

        if ($this->option('all')) {
            return $this->deleteAllMailables();
        }

        return $this->deleteSingleMailable();
    }

    private function deleteAllMailables()
    {
        if (!$this->confirmDeletion('all mailables')) {
            return 1;
        }

        $preserveOption = $this->option('preserve');

        if (!$preserveOption || $preserveOption === 'files') {
            $deletedCount = Mailable::count();
            Mailable::truncate();
            $this->info("Deleted {$deletedCount} database records");
        }

        if (!$preserveOption || $preserveOption === 'database') {
            if (File::exists($this->baseMailablePath)) {
                File::deleteDirectory($this->baseMailablePath);
                $this->info('Deleted all mailable files');
            }
        }

        $this->info('All mailables have been deleted successfully');
        return 0;
    }

    private function deleteSingleMailable()
    {
        $className = $this->option('class');
        $fullClassName = "App\\Mailables\\{$className}";

        if (!$this->confirmDeletion($className)) {
            return 1;
        }

        $preserveOption = $this->option('preserve');

        if (!$preserveOption || $preserveOption === 'files') {
            $mailable = Mailable::whereMailable($fullClassName)->first();
            if ($mailable) {
                $mailable->delete();
                $this->info("Deleted database record for {$className}");
            }
        }

        if (!$preserveOption || $preserveOption === 'database') {
            $filePath = "{$this->baseMailablePath}/{$className}.php";
            if (File::exists($filePath)) {
                File::delete($filePath);
                $this->info("Deleted file for {$className}");
            }
        }

        $this->info("{$className} has been deleted successfully");
        return 0;
    }

    private function confirmDeletion($target)
    {
        return $this->confirm(
            "Are you sure you want to delete {$target}? This action cannot be undone.",
            false
        );
    }
}
