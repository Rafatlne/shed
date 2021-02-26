<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\NspService946;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Handler\CurlHandler;
use Rap2hpoutre\FastExcel\FastExcel;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class DownloadFourtySix extends Command
{
/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:download-946';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download all data as a excel from nsp_service table';

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

        $start = 0;
        $limit = 80;
        $excel_start = 739;
        $excel_end = 842;

        for ($i = 0; $i < 1; $i++) {
            $directory = storage_path("upload");
            $file = $directory . '\shed-946-' . $excel_start . '-' . $excel_end . '.xlsx';
            (new FastExcel($this->getApplicationOneByOne($start, $limit)))->export($file);
            $start = $start + 101;
            $limit = $limit + 100;
            $excel_start = $excel_start + 1001;
            $excel_end = $excel_end + 1000;
        }

        // return (new FastExcel($this->getApplicationOneByOne()))->export($file);
    }

    // Generator function
    private function getApplicationOneByOne($start, $limit)
    {
        
        $nsp_services = NspService946::all();

        $bar = $this->output->createProgressBar(count($nsp_services));

        $bar->start();
        $current_time = $this->getCurrentTime();

        // build your chunks as you want (200 chunks of 10 in this example)
        for ($i = $start; $i < $limit; $i++) {
            $nsp_services = DB::table('nsp_service_946')->where('created_at', '<=', $current_time)->skip($i * 10)->take(10)->get();

            foreach ($nsp_services as $nsp_service) {
                $temp_application_data = [];
                $temp_application_data =  $this->getDataAsArray($nsp_service->DATA);
                array_push($temp_application_data, $nsp_service->aid);
                array_push($temp_application_data, $nsp_service->applicant_mobile);
                $attachments = json_decode($nsp_service->attachment, true);
                if (!empty($attachments) || isset($attachments)) {
                    foreach ($attachments as $attachment) {
                        if ($attachment != "" || !empty($attachment)) {
                            array_push($temp_application_data, $this->getAttachmentURL($attachment));
                        }
                    }
                }

                $bar->advance();
                yield $temp_application_data;
            }
        }
        $bar->finish();
    }



    private function getCurrentTime()
    {
        $current_time = Carbon::now();
        return $current_time->toDateTimeString();
    }

    private function getDataAsArray($datas)
    {
        $form_labels = [];
        $temp_data = [];
        $datas = json_decode($datas, true);
        $form_labels = $this->getFormLabels();
        $temp_data = array_merge($form_labels, $datas);
        return array_values($temp_data);
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

    private function getFormLabels()
    {
        return [
            "applicant_type" => 0,
            "division-1610521464843" => "----",
            "district-1610521467213" => "----",
            "upazila-1610521470438" => "----",
            "institutions-9601610523383482" => "----",
            "eiin-9601610520904683" => "----",
            "institute_head_nid" => "----",
            "institute_head_nid_name_en" => "----",
            "institute_head_nid_name" => "----",
            "form-9601609047985178" => "----",
            "form-9601610523363197" => "----",
            "form-9461611746947937" => "----",
            "form-9461612068930574" => "----",
            "form-9461611748492035" => "----",
            "form-9461611748526012" => "----",
            "form-9461611748601293" => "----",
            "fid" => "----",
            "auto-send" => "----",
            "is_complete" => "----",
            "auto_to_whom" => "----",
            "auto_office_name" => "----",
            "auto_office_address" => "----",
            "office_attention_desk_unit" => "----",
            "name" => "----",
            "name_en" => "----",
            "mname" => "----",
            "fname" => "----",
            "dob" => "----",
            "national_id_no" => "----",
            "birth_certificate_no" => "----"
        ];
    }

    private function getAttachmentURL($fid)
    {
        $url = 'https://eksheba.gov.bd/api/get_attachment_link?fid=' . $fid;
        $handlerStack = HandlerStack::create(new CurlHandler());
        $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
        $client = new Client(array('handler' => $handlerStack));
        $response = $client->request('GET', $url);
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        return json_decode($body);
    }

    private function retryDecider()
    {
        return function (
            $retries,
            \GuzzleHttp\Psr7\Request $request,
            \GuzzleHttp\Psr7\Response $response = null,
            \GuzzleHttp\Exception\ConnectException $exception = null
        ) {
            // Limit the number of retries to 5
            if ($retries >= 5) {
                return false;
            }

            // Retry connection exceptions
            if ($exception instanceof \GuzzleHttp\Exception\ConnectException) {
                return true;
            }

            if ($response) {
                // Retry on server errors
                if ($response->getStatusCode() >= 500) {
                    return true;
                }
            }

            return false;
        };
    }

    private function retryDelay()
    {
        return function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }
}
