<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Audit;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
// use App\Jobs\CustomersJob;
use App\Models\User;
//use pusher
use Pusher\Pusher;
use App\Notifications\TestNotification;


class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $history = Audit::where("auditable_type","App\Models\Order")->get();
        return view('admin.history.index', compact('history'));
    }

    // function getAccessToken()
    // {
    //     $client = new \GuzzleHttp\Client();
    //     $response = $client->request('POST', 'https://id.kiotviet.vn/connect/token', [
    //         'headers' => [
    //             'Content-Type' => 'application/x-www-form-urlencoded',
    //         ],
    //         'form_params' => [
    //             'scopes' => 'PublicApi.Access',
    //             'grant_type' => 'client_credentials',
    //             'client_id' => '83a5bcbe-3c39-458c-bdd9-128112cef3f7',
    //             'client_secret' => '3B52F3A9DDE194966DAE2CE0A478B2DEC15254D6',
    //         ],
    //     ]);

    //     $body = json_decode((string) $response->getBody());
    //     $access_token = $body->access_token;

    // }

    // function getCategories($lastModifiedFrom = null, $pageSize = null, $currentItem = null, $orderBy = null, $orderDirection = 'Asc', $hierarchicalData = false)
    // {
    //     $client = new Client();
    //     $headers = [
    //     'Retailer' => 'angrico',
    //     'Authorization' => 'Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6ImF0K2p3dCJ9.eyJuYmYiOjE2ODMxNjcwMjcsImV4cCI6MTY4MzI1MzQyNywiaXNzIjoiaHR0cDovL2lkLmtpb3R2aWV0LnZuIiwiY2xpZW50X2lkIjoiMzlhZTQ2MjMtMGNhOS00OTViLTlkMWMtYjFkZGJlZDNmMWQ2IiwiY2xpZW50X1JldGFpbGVyQ29kZSI6ImFuZ3JpY28iLCJjbGllbnRfUmV0YWlsZXJJZCI6IjUwMDIwODA0NyIsImNsaWVudF9Vc2VySWQiOiIzNDYwOTIiLCJjbGllbnRfU2Vuc2l0aXZlQXBpIjoiVHJ1ZSIsImlhdCI6MTY4MzE2NzAyNywic2NvcGUiOlsiUHVibGljQXBpLkFjY2VzcyJdfQ.XgsJLjAFUcgt9Lx6_bKJm8Q36lTDG2p1P4V6o4VOhhR57UF-JJPVfX5nhZidgoT_b8ydOoae-u-uaF3p3vSjosvV2x-XcjQCxpKD_diDnANI5SqT8MHBZvrxC2cw6SdDWSwUsowFUT1A6eU1NsAvIZP5kFYuEHQpVz4ZOfbTsHYeoWxXD8heM8XSZxux8BKuHb_XNB1TSFCIX-sZZ8Yf1H3upp8-9Vm_YM226PjenEdhIb3UFo8IC800eGP6WT9ebJvIIgG0X-iX6BjxVt8N3ZNjhJFZEt1vUjdGav_maf7eP2aatETjKdbg_oelijm0EtUc7s4z_UlbA9MCZKQSkA',
    //     'Cookie' => 'ss-id=R84WdJI1TNY8mGSO3erU; ss-pid=sX7osIvgPCH3Jpnysxkd'
    //     ];
    //     $request = new Request('GET', 'https://public.kiotapi.com/customers', $headers);
    //     $res = $client->sendAsync($request)->wait();
    //     echo $res->getBody();

    // }


    // public function fetchCustomers($page = 1) {
    //     $latestCustomer = Customer::orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();
    //     $currentDateTime = Carbon::now()->format('d/m/Y');

    //     if($latestCustomer){
    //         $jsonData = $latestCustomer->data;
    //         $dataObj = json_decode($jsonData);
    //         $dateCreated = $dataObj->datetime_created;
    //     } else {
    //         $dateCreated = "01/01/2000";
    //     }

    //     dd($this->getAccessToken());
    //     // dd($dateCreated);


    //     $headers = ['Cookie' => 'PHPSESSID=39u5mrdnr0soq1gsbp5bjhfdc8'];

    //     $response = Http::withHeaders($headers)->get('https://demo.1office.vn/api/customer/customer/gets', [
    //         'access_token' => '12222983856448ed837e76d790469725',
    //         'page' => $page,
    //         'filters' => '[{"date_created_from":"'.$dateCreated.'", "date_created_to":"'.$currentDateTime.'"}]'
    //     ]);

    //     // dd($response->json());

    //     if ($response->ok() && $response->json()['error']==false) {
    //         $customers = $response->json()['data'];
    //         if (count($customers) > 0) {
    //             foreach ($customers as $customer) {
    //                 $customerCode = $customer['code'];
    //                 if($customer['date_created'] == $dateCreated){

    //                     if(!Customer::where('customer_code', $customerCode)->exists()){
    //                         $newCustomer = new Customer;
    //                         $newCustomer->customer_code = $customerCode;
    //                         $newCustomer->data = json_encode($customer);
    //                         $newCustomer->synced_data = "{'1office':'1','sapo':'0','kiotviet':'0'}";
    //                         $newCustomer->note = "Lưu dữ liệu từ 1office về";
    //                         $newCustomer->save();
    //                     }
    //                 }else{
    //                     // dd("ssss");
    //                     $newCustomer = new Customer;
    //                     $newCustomer->customer_code = $customerCode;
    //                     $newCustomer->data = json_encode($customer);
    //                     $newCustomer->synced_data = "{'1office':'1','sapo':'0','kiotviet':'0'}";
    //                     $newCustomer->note = "Lưu dữ liệu từ 1office về";
    //                     $newCustomer->save();
    //                 }
    //             }
    //             $total_item = $response->json()['total_item'];
    //             if ($total_item > ($page * 50)) {
    //                 $nextPage = $page + 1;
    //                 $this->fetchCustomers($nextPage);
    //             } else {
    //                 return 1;
    //             }
    //         } else {
    //             return 2;
    //         }
    //     } else {
    //         return 3;
    //     }
    // }

    public function create()
    {
        $this->getCategories();
        // switch ($this->fetchCustomers()) {
        //     case 1:
        //         return redirect()->route('customers.index')->with('success', 'Đồng bộ dữ liệu thành công');
        //         break;
        //     case 2:
        //         return redirect()->route('customers.index')->with('success', 'Hiện tại đang là dữ liệu mới nhất');
        //         break;
        //     case 3:
        //         return redirect()->route('customers.index')->with('success', 'Có lỗi xảy ra');
        //         break;
        // }
    }

    public function store(Request $request)
    {
        // $customer = new Customer();
        // $customer->customer_code = $request->input('customer_code');
        // $customer->synced_data = $request->input('synced_data');
        // $customer->data = $request->input('data');
        // $customer->note = $request->input('note');
        // $customer->save();

        // return redirect()->route('customers.index');
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        // $customer = Customer::find($id);
        // $customer->customer_code = $request->input('customer_code');
        // $customer->synced_data = $request->input('synced_data');
        // $customer->data = $request->input('data');
        // $customer->note = $request->input('note');
        // $customer->save();

        // return redirect()->route('customers.index');
    }

    public function destroy($id)
    {
        // $customer = Customer::find($id);
        // $customer->delete();

        // return redirect()->route('customers.index');
    }
}
