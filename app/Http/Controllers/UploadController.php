<?php

namespace App\Http\Controllers;

use App\Models\AuctionFileUploaded;
use App\Models\BidderLogin;
use App\Models\BiddingInfo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    // Upload File Page
    public function index()
    {
        return view('upload_files');
    }

    public function uploadToServer(Request $request)
    {
        try {

            $fileObj = $request->file('file');
            $extension = $fileObj->getClientOriginalExtension();
            $validExtension = ['xls', 'xlsx', 'csv'];

            // check extension
            if (!in_array($extension, $validExtension)) {
                return response()->json(['status' => 'failure','message' => 'Required file type : xls, xlsx, csv,']);
            }

            // fileanme
            $fileName = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
            $name = $fileName . '.' . time() . "." . $extension;

            // move the file
            $request->file->move(public_path('uploads'), $name);

            $file = new AuctionFileUploaded();
            $filePath = public_path() . "/uploads/" . $name;

            // Php spreadsheet object to get the file type and open in reader mode
            $inputFileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($inputFileType);
            $spreadsheet = $reader->load($filePath);

            // get active sheet
            $worksheet = $spreadsheet->getActiveSheet();

            // get max row and column in uploaded sheet
            $maxCell = $worksheet->getHighestRowAndColumn();
            $columnCount = Coordinate::columnIndexFromString($maxCell['column']);
            $rowCount = $maxCell['row'];

            // Loop through the entire row and get the value from the uploaded sheet
            for ($i = 2; $i < $rowCount; $i++) {

                $bidderLogin = new BidderLogin();
                $bidderInfo = new BiddingInfo();

                // get the values from uploaded sheet by cell row and column number
                $date = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $i)->getCalculatedValue();
                $ipAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, $i)->getValue();
                $action = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $i)->getValue();
                $state = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, $i)->getValue();
                $run = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5, $i)->getValue();
                $lot = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, $i)->getValue();
                $item = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(7, $i)->getValue();
                $bidder = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(8, $i)->getValue();
                $user = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(9, $i)->getValue();
                $amount = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(10, $i)->getValue();
                $pageUrl = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(11, $i)->getValue();
                $useragent = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(12, $i)->getValue();
                $sessionNumber = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(13, $i)->getValue();

                // check the condition id the action is Bidder - Logged in saved the data in bidder login table
                if ($action == "Bidder - Logged in") {

                    // create a bidder login object  and save it
                    $bidderLogin->date = $date;
                    $bidderLogin->ip_address = $ipAddress;
                    $bidderLogin->state = $state;
                    $bidderLogin->run = $run;
                    $bidderLogin->lot = $lot;
                    $bidderLogin->item = $item;
                    $bidderLogin->bidder = $bidder;
                    $bidderLogin->user = $user;
                    $bidderLogin->amount = $amount;
                    $bidderLogin->page_url = $pageUrl;
                    $bidderLogin->useragent = $useragent;
                    $bidderLogin->session_number = $sessionNumber;

                    $bidderLogin->save();

                    // Logging info to fileprocessinginfo log file
                    Log::channel('fileprocessinginfo')->info($fileName . "." . $extension .  " is processing and " . $i . " row has been inserted
                    successfully in to the Bidder login table");

                } else {

                    // create a bidder info object  and save it
                    $bidderInfo->date = $date;
                    $bidderInfo->ip_address = $ipAddress;
                    $bidderInfo->state = $state;
                    $bidderInfo->run = $run;
                    $bidderInfo->lot = $lot;
                    $bidderInfo->item = $item;
                    $bidderInfo->bidder = $bidder;
                    $bidderInfo->user = $user;
                    $bidderInfo->amount = $amount;
                    $bidderInfo->page_url = $pageUrl;
                    $bidderInfo->useragent = $useragent;
                    $bidderInfo->session_number = $sessionNumber;
                    $bidderInfo->save();

                    // Logging info to fileprocessinginfo log file
                    Log::channel('fileprocessinginfo')->info($fileName . "." . $extension .  " is processing and " . $i . " row has been inserted
                    successfully in to the Bidder Info table");

                }
            }

            // Save the file details in Auction File  table
            $file->file_name = $fileName . "." . $extension;
            $file->file_rows_count = $rowCount;
            $file->is_uploaded = true;
            $file->save();

            // Logging info to fileprocessinginfo log file
            Log::channel('fileprocessinginfo')->info($fileName . "." . $extension .  " is processed and file info has been inserted
                    successfully in to the Auction File Uploaded table");

            return response()->json(['status' => 'success', 'message' => 'Successfully uploaded.']);

        } catch (QueryException $e) {

            // Logging info to fileprocessingerror log file
            Log::channel('fileprocessingerror')->error($e->getMessage());
            return response()->json(['status' => 'failure', 'message' => "Query Exception"]);

        } catch (\Exception $e) {

            // Logging info to fileprocessingerror log file
            Log::channel('fileprocessingerror')->error($e->getMessage());
            return response()->json(['status' => 'failure', 'message' => $e->getMessage()]);
        }
    }
}
