<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UploadNginxLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nginx:upload:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $logFileNameFormat = 'accesslog_%s';
    private $logDateFormat = 'Y-m-D';

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
     * @return int
     */
    public function handle()
    {

        $location = app_path('../logs');
        $filePath = realpath($location).'/grasengroen.nl.log.1';

        $dateToFind = date('d/M/Y', strtotime('-1day'));

        $fp = fopen($filePath,'r');
        $logContent = '';
        while($fp && !feof($fp)){

            $line = fgets($fp);
            if(strpos($line, $dateToFind) !== false){
                $logContent.= $line;
            }
        }

        $fileName = $this->getFileName();

        $path = Storage::disk('s3')->put('/'.$fileName, $logContent);

        return 0;
    }

    protected function getFileName()
    {
        $filename = sprintf($this->logFileNameFormat, date($this->logDateFormat));
        return $filename;
    }

}
