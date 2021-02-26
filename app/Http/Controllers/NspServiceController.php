<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\NspService;
use GuzzleHttp\Client;
use PhpParser\JsonDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class NspServiceController extends Controller
{
    public function lazyWrite()
    {
        // $directory = storage_path("upload");
        // $file = fopen($directory . "\shed-22-02-2021.csv", "w+");
        // Export consumes only a few MB
        return (new FastExcel($this->getUsersOneByOne()))->export('text.xlsx');
        // $current_time = $this->getCurrentTime();
        // $users = \App\NspService::where('created_at', '<=', $current_time)->cursor()
        //     ->each(function ($nsp_service) use ($file) {
        //         $temp_application_data = [];
        //         $nsp_service = $nsp_service->toArray();

        //         $temp_application_data =  $this->getDataAsArray($nsp_service["DATA"]);
        //         array_push($temp_application_data, $nsp_service['aid']);
        //         array_push($temp_application_data, $nsp_service['applicant_mobile']);
        //         $attachments = json_decode($nsp_service['attachment'], true);
        //         foreach ($attachments as $attachment) {
        //             if ($attachment != "" || !empty($attachment)) {
        //                 array_push($temp_application_data, $this->getAttachmentURL($attachment));
        //             }
        //         }

        //         fputcsv($file, $temp_application_data);
        //     });
        // fclose($file);
    }

    // Generator function
    private function getUsersOneByOne()
    {
        // build your chunks as you want (200 chunks of 10 in this example)
        $current_time = $this->getCurrentTime();
        // for ($i = 0; $i < 2; $i++) {
        //     $users = DB::table('nsp_service')->where('created_at', '<=', $current_time)->skip($i * 10)->take(10)->get();
        //     // Yield user one by one
            foreach (NspService::where('created_at', '<=', $current_time)->cursor() as $nsp_service) {
                $temp_application_data = [];
                $temp_application_data =  $this->getDataAsArray($nsp_service->DATA);
                array_push($temp_application_data, $nsp_service->aid);
                array_push($temp_application_data, $nsp_service->applicant_mobile);
                $attachments = json_decode($nsp_service->attachment, true);
                foreach ($attachments as $attachment) {
                    if ($attachment != "" || !empty($attachment)) {
                        array_push($temp_application_data, $this->getAttachmentURL($attachment));
                    }
                }

                yield $temp_application_data;
                
            }
        // }

        // NspService::where('created_at', '<=', $current_time)->cursor()
        //     ->each(function ($nsp_service){
        //         $temp_application_data = [];
        //         $nsp_service = $nsp_service->toArray();

        //         $temp_application_data =  $this->getDataAsArray($nsp_service["DATA"]);
        //         array_push($temp_application_data, $nsp_service['aid']);
        //         array_push($temp_application_data, $nsp_service['applicant_mobile']);
        //         $attachments = json_decode($nsp_service['attachment'], true);
        //         foreach ($attachments as $attachment) {
        //             if ($attachment != "" || !empty($attachment)) {
        //                 array_push($temp_application_data, $this->getAttachmentURL($attachment));
        //             }
        //         }
        //         dd($temp_application_data);
        //         yield $temp_application_data;
        //     });
    }



    private function getCurrentTime()
    {
        $current_time = Carbon::now();
        return $current_time->toDateTimeString();
    }

    private function getDataAsArray($datas)
    {
        $datas = json_decode($datas);
        $temp_data = [];

        foreach ($datas as $key => $value) {
            $temp_data[] = $value;
        }
        return $temp_data;
    }

    private function getAttachment($attachments)
    {
        $temp_attachments = [];
        foreach ($attachments as $attchment) {
            if ($attchment != "") {
                array_push($temp_attachments, $this->getAttachmentURL($attchment));
            }
        }
        return $temp_attachments;
    }
    private function getAttachmentURL($fid)
    {
        $url = 'https://eksheba.gov.bd/api/get_attachment_link?fid=' . $fid;
        $client = new Client();
        $response = $client->request('GET', $url);
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        return json_decode($body);
    }
}
