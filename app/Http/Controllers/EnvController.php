<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnvController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show1Office()
    {
        $data = [
            '1OFFICE_ACCESS_TOKEN' => env('1OFFICE_ACCESS_TOKEN'),
            '1OFFICE_APINAME' => env('1OFFICE_APINAME'),
            '1OFFICE_HOST' => env('1OFFICE_HOST'),
            '1OFFICE_CF_COUNTRY' => env('1OFFICE_CF_COUNTRY'),
            '1OFFICE_CF_ADDRESS_LINE_1' => env('1OFFICE_CF_ADDRESS_LINE_1'),
            '1OFFICE_CF_ADDRESS_LINE_2' => env('1OFFICE_CF_ADDRESS_LINE_2'),
            '1OFFICE_CF_CITY' => env('1OFFICE_CF_CITY'),
            '1OFFICE_CF_STATE' => env('1OFFICE_CF_STATE'),
            '1OFFICE_CF_ZIP_CODE' => env('1OFFICE_CF_ZIP_CODE'),
            '1OFFICE_CF_ADDRESS_VERIFICATION_STATUS' => env('1OFFICE_CF_ADDRESS_VERIFICATION_STATUS'),
            '1OFFICE_CF_ORDERS_STATUS' => env('1OFFICE_CF_ORDERS_STATUS'),
            '1OFFICE_CF_ORDERS_ADDRESS_FULL' => env('1OFFICE_CF_ORDERS_ADDRESS_FULL'),
            '1OFFICE_CF_ORDERS_CUSTOMER_NOTES' => env('1OFFICE_CF_ORDERS_CUSTOMER_NOTES'),
            '1OFFICE_CF_ORDERS_INTERNAL_NOTES' => env('1OFFICE_CF_ORDERS_INTERNAL_NOTES'),
            '1OFFICE_CF_ORDERS_RATE' => env('1OFFICE_CF_ORDERS_RATE'),
            '1OFFICE_CF_ORDERS_TRACKING_NUMBER' => env('1OFFICE_CF_ORDERS_TRACKING_NUMBER'),
        ];

        return view('admin.settingApi.settingApi1Office', compact('data'));
    }

    public function update1Office(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            if ($key != '_token') {
                $this->setEnvValue($key, $value);
            }
        }

        return redirect()->back()->with('success', 'Updated successfully');
    }

    private function setEnvValue($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $fileContent = file_get_contents($path);
            $keyPosition = strpos($fileContent, "$key=");

            if ($keyPosition !== false) {
                $endOfLinePosition = strpos($fileContent, PHP_EOL, $keyPosition);
                $oldLine = substr($fileContent, $keyPosition, $endOfLinePosition - $keyPosition);
                $newLine = "$key=$value";
                $fileContent = str_replace($oldLine, $newLine, $fileContent);
            } else {
                $fileContent .= PHP_EOL . "$key=$value";
            }

            file_put_contents($path, $fileContent);
        }
    }
}
