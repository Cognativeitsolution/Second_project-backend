<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use File;
use Mail;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //return Command::SUCCESS;
        $data["email"] = "tahseenxgfx@gmail.com";
        $data["title"] = "Employment Agency";
        $data["body"] = "This is testing Email";

        $path = storage_path('app/Laravel/');
        $filesInFolder = File::allFiles($path);
        $max_key = max(array_keys($filesInFolder));

        foreach($filesInFolder as $key => $path){
            $files = pathinfo($path);
            $allMedia[] = $files['basename'];
        }

        $files = [
            storage_path('app/Laravel/').$allMedia[$max_key],
        ];

        Mail::send('emails.myTestMail', $data, function($message)use($data, $files) {
            $message->to($data["email"], $data["email"])
                ->cc([
                    'tahseen.developer@gmail.com',
                    //'tahseenxgfx@gmail.com',
                ])
                ->subject($data["title"]);

            foreach ($files as $file){
                $message->attach($file);
            }

        });


    }
}
