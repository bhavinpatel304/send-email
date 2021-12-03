<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Redis;
use Mail;
use Storage;
use Log;
use PDF;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    protected $file_attachments;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $file_attachments)
    {  
        $this->data = $data;
        $this->file_attachments = $file_attachments;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data["email"] = $this->data ;
        $data["title"] = "new email" ;

        $file_attachments = $this->file_attachments;
        $email_body = new SendEmail();

        foreach($data["email"]  as $to){
            Mail::to($to)->send($email_body,function($message)use($data,$file_attachments) {
                $message->subject($data["title"]);                    
                foreach ($file_attachments as $file){
                    $arr = explode("/" ,  $file);    
                    $message->attachData(base64_decode($file), end( $arr ) );
                }            
            });
        }
        
    }
}