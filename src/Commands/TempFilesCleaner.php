<?php

namespace Elgndy\FileUploader\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Elgndy\FileUploader\Services\MediaDeleterService;

class TempFilesCleaner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elgndy:cleaner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will remove the created files for more than configured time';

    /**
     * The media deleter service.
     *
     * @var MediaDeleterService
     */
    protected $mediaDeleterService;

    /**
     * Create a new command instance.
     */
    public function __construct(MediaDeleterService $dms)
    {
        $this->mediaDeleterService = $dms;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $removedCount = $this->mediaDeleterService->cleanTempFolder();
        Log::info('The number of removed files is '.$removedCount);
    }
}
