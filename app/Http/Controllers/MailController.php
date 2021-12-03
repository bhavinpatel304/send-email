<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Mail;
use Storage;
use PDF;

class MailController extends Controller
{
    public function sendEmail(Request $request){

   
        $validator = Validator::make($request->all(), 
                    [
                        'to_mail' => 'required',
                        'to_mail.*' => 'required',
                        'file_attachments' => 'required|max:2048',
                        'file_attachments.*' => 'required|max:2048',
                    ]);
        
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors() ] , 200);
        }
        
        $to_mail = $request->post("to_mail");
        $file_attachments =  $request->file("file_attachments");

        foreach($file_attachments as $file){
            $name =  time();            
            $path = Storage::disk('public')->put($name, $file);
            $files[] = public_path('storage') ."/". $path;            
        }

        try{
            dispatch(new \App\Jobs\SendEmailJob($to_mail, $files));
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    
        return response()->json(["Perfect Email Execution"]);

        
        
    }
}
