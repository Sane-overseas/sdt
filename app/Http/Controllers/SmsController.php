<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use App\Models\AsignedSchool;
use Session;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    public function sendMessage(Request $request){

        $message = "Respected {#var#}, We are looking for help from your side to find Martial Arts instructors in {#var#} with 6 month certification course or martial art training experience certificate. Interested Candidate may call at +91 8360637949. Regards, Sane Overseas Pvt Ltd";
        $mobile = "917807264381";
    
        $apiURL = "http://103.153.58.130/api/v2/SendSMS?SenderId=VCSGRP&Is_Unicode=false&Is_Flash=false&Message=".$message."&MobileNumbers=".$mobile."&ApiKey=rEAmiGZw1SRWcKPa1xKruemOsk8/XqA4jPry3p95FHw=&ClientId=61e90bff-bd04-4abe-ab5c-f452f3a921bd";
       
        $postInput = [
            'MobileNumbers' => $mobile,
            'Message' => $message,
        ];
                
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $apiURL, ['form_params' => $postInput]);
     
        $statusCode = $response->getStatusCode();
        $responseBody = json_decode($response->getBody(), true);
    
        dd($response,$responseBody);

    }

}
