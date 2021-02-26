<?php

namespace App\Console\Commands;

use App\Enums\Service944;
use Carbon\Carbon;
use App\NspService;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Handler\CurlHandler;
use Rap2hpoutre\FastExcel\FastExcel;

class DownloadExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:download';

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

        $start = 73;
        $limit = 173;
        $excel_start = 18001;
        $excel_end = 19000;

        for ($i = 0; $i < 3; $i++) {
            $directory = storage_path("upload");
            $file = $directory . '\shed-944-' . $excel_start . '-' . $excel_end . '.xlsx';
            (new FastExcel($this->getApplicationOneByOne($start, $limit)))->export($file);
            $start = $start + 100;
            $limit = $limit + 100;
            $excel_start = $excel_start + 1000;
            $excel_end = $excel_end + 1000;
        }

        // return (new FastExcel($this->getApplicationOneByOne()))->export($file);
    }

    // Generator function
    private function getApplicationOneByOne($start, $limit)
    {
        // build your chunks as you want (200 chunks of 10 in this example)
        $nsp_services = NspService::all();

        $bar = $this->output->createProgressBar(count($nsp_services));

        $bar->start();
        $current_time = $this->getCurrentTime();

        // foreach (NspService::where('created_at', '<=', $current_time)->cursor() as $nsp_service) {
        // 1157
        //
        // for ($i = 200; $i < 250; $i++) {
        for ($i = $start; $i < $limit; $i++) {
            $nsp_services = DB::table('nsp_service')->where('created_at', '<=', $current_time)->skip($i * 10)->take(10)->get();

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
        // print_r($temp_data);
        // $temp_data = [];
        // $key_exist = true;
        // foreach ($datas as $key => $value) {
        //         $temp_data[] = $value;
        // }
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

    private function compareKeysOfData($key)
    {
        switch ($key) {
            case Service944::APPLICANT_TYPE:
                echo true;
                break;
            case Service944::DIVISION_1609046733364:
                echo true;
                break;
            case Service944::DISTRICT_1609046736419:
                echo true;
                break;
            case Service944::DISTRICT_1609046736419:
                echo true;
                break;
            case Service944::INSTITUTIONS_9601610523621183:
                echo true;
                break;
            case Service944::EIIN_9601609046827782:
                echo true;
                break;
            case Service944::FORM_9601609046829170:
                echo true;
                break;
            case Service944::FORM_9601609046823602:
                echo true;
                break;
            case Service944::FORM_9601609047046820:
                echo true;
                break;
            case Service944::FORM_9601610521644149:
                echo true;
                break;
            case Service944::FORM_9601609047140238:
                echo true;
                break;
            case Service944::MOBILE_BANKING:
                echo true;
                break;
            case Service944::FORM_9601609047288077:
                echo true;
                break;
            case Service944::SCHOLARSHIP:
                echo true;
                break;
            case Service944::STUDENT_NID_FATHER:
                echo true;
                break;
            case Service944::STUDENT_NID_FATHER_NAME:
                echo true;
                break;
            case Service944::FORM_9601609047055840:
                echo true;
                break;
            case Service944::FORM_9601609046822074:
                echo true;
                break;
            case Service944::FORM_9441611224908114:
                echo true;
                break;
            case Service944::FORM_9601609046818946:
                echo true;
                break;
            case Service944::FORM_9601609047060435:
                echo true;
                break;
            case Service944::FORM_9601609047059045:
                echo true;
                break;
            case Service944::FORM_9601609047061853:
                echo true;
                break;
            case Service944::FORM_9601609047057618:
                echo true;
                break;
            case Service944::PHYDISABLITY:
                echo true;
                break;
            case Service944::FORM_9441611745127583:
                echo true;
                break;
            case Service944::FORM_9441612069114977:
                echo true;
                break;
            case Service944::FORM_9441611745354360:
                echo true;
                break;
            case Service944::FORM_9441611747675062:
                echo true;
                break;
            case Service944::FID:
                echo true;
                break;
            case Service944::IS_COMPLETE:
                echo true;
                break;
            case Service944::AUTO_TO_WHOM:
                echo true;
                break;
            case Service944::AUTO_OFFICE_NAME:
                echo true;
                break;
            case Service944::AUTO_OFFICE_ADDRESS:
                echo true;
                break;
            case Service944::OFFICE_ATTENTION_DESK_UNIT:
                echo true;
                break;
            case Service944::NAME:
                echo true;
                break;
            case Service944::NAME_EN:
                echo true;
                break;
            case Service944::MNAME:
                echo true;
                break;
            case Service944::FNAME:
                echo true;
                break;
            case Service944::DOB:
                echo true;
                break;
            case Service944::NATIONAL_ID_NO:
                echo true;
                break;
            default:
                echo false;
                break;
        }
    }

    private function getFormLabels()
    {
        return [
            "applicant_type" => 0,
            "division-1609046733364" => "----",
            "district-1609046736419" => "----",
            "upazila-1609046741758" => "----",
            "institutions-9601610523621183" => "----",
            "eiin-9601609046827782" => "----",
            "form-9601609046829170" => "----",
            "form-9601609046823602" => "----",
            "form-9601609047046820" => "----",
            "form-9601610521644149" => "----",
            "form-9601609047140238" => "----",
            "mobile-banking" => "----",
            "form-9601609047288077" => "----",
            "scholarship" => "----",
            "student_nid_father" => "----",
            "student_nid_father_name" => "----",
            "form-9601609047055840" => "----",
            "form-9601609046822074" => "----",
            "form-9441611224908114" => "----",
            "form-9601609046818946" => "----",
            "form-9601609047060435" => "----",
            "form-9601609047059045" => "----",
            "form-9601609047061853" => "----",
            "form-9601609047057618" => "----",
            "phy-disablity" => "----",
            "form-9441611745127583" => "----",
            "form-9441612069114977" => "----",
            "form-9441611745354360" => "----",
            "form-9441611747675062" => "----",
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
        // $client = new Client();
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
