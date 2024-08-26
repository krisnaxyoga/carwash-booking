<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\CarService;
use App\Models\CarStore;
use App\Models\BookingTransaction;
use App\http\Requests\StoreBookingRequest;
use App\http\Requests\StoreBookingPaymentRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class FrontController extends Controller
{
    public function index()
    {
        $cities = City::all();
        $services = CarService::withCount(['storeServices'])->get();
        return view('front.index',compact('cities','services'));
    }
    public function search(Request $request){
        $city_id = $request->city_id;
        $services = $request->service_type;
        $carServices = CarService::where('id',$services)->first();
        if(!$carServices){
            return redirect()->back()->with('error','Service not found');
        }

        $carStore = CarStore::whereHas('storeServices', function ($query) use ($carServices) {
            $query->where('car_service_id', $carServices->id);
        })->where('id_city',$city_id)->get();

        $city = City::find($city_id);
        session()->put('serviceTypeId', $request->input('service_type'));

        return view('front.search',['carServices'=>$carServices,'store'=>$carStore,'city_name'=>$city ? $city->name : 'unknown']); 
    }   
 
    public function details(CarStore $carStore)
    {
        $servicetypeid = session()->get('serviceTypeId');
        $carServices = CarService::where('id',$servicetypeid)->first();
        return view('front.details',compact('carStore','carServices'));
    }

    public function booking(CarStore $carStore)
    {
        session()->put('carStoreId', $carStore->id);
        $servicetypeid = session()->get('serviceTypeId');
        $service = CarService::where('id',$servicetypeid)->first();
        return view('front.booking',compact('carStore','service'));
    }

    public function booking_store(StoreBookingRequest $request){
        $customername = $request->name;
        $customerphone = $request->phone_number;
        $customerTimeAt = $request->time_at;
        
        session()->put('customerName', $customername);
        session()->put('customerPhone', $customerphone);
        session()->put('customerTimeAt', $customerTimeAt);

        $servicetypeid = session()->get('serviceTypeId');
        $carstoreid = session()->get('carStoreId');

        return redirect()->route('front.booking.payment',[$carstoreid,$servicetypeid]);
    }

    public function booking_payment(CarStore $carStore,CarService $carService){
        $ppn = 0.11;
        $totalPpn = $carService->price * $ppn;
        $bookingfee = 25000;
        $totalGrandTotal = $totalPpn + $bookingfee + $carService->price;
        session()->put('totalAmount', $totalGrandTotal);
        return view('front.payment',compact('carService','carStore','totalGrandTotal','totalPpn','bookingfee'));
    }

    public function booking_payment_store(StoreBookingPaymentRequest $request){
        $customername = session()->get('customerName');
        $customerphone = session()->get('customerPhone');
        $customerTimeAt = session()->get('customerTimeAt');
        $carstoreid = session()->get('carStoreId');
        $totalAmount = session()->get('totalAmount');
        $servicetypeid = session()->get('serviceTypeId');

        $bookingTransactionId =null;
       
        DB::transaction(function() use ($request, $customername, $customerphone, $customerTimeAt, $carstoreid, $totalAmount, $servicetypeid, &$bookingTransactionId) {
            $validated = $request->validated();
            if($request->hasFile('proof')){
                $proofPath = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }

            $validated['name'] = $customername;
            $validated['phone_number'] = $customerphone;
            $validated['time_at'] = $customerTimeAt;
            $validated['car_store_id'] = $carstoreid;
            $validated['total_amount'] = $totalAmount;
            $validated['car_service_id'] = $servicetypeid;
            $validated['started_at'] = Carbon::tomorrow()->format('Y-m-d');
            $validated['is_paid'] = false;
            $validated['trx_id'] = BookingTransaction::generateUniqueTrxId();

            $newBooking = BookingTransaction::create($validated);
            $bookingTransactionId = $newBooking->id;
        });
        return redirect()->route('front.success.booking', $bookingTransactionId);
    }
    public function success_booking(BookingTransaction $bookingTransaction){
        return view('front.success_booking',compact('bookingTransaction'));
    }

    public function transactions(){
        return view('front.transactions');
    }

    public function transactions_details(Request $request){
        $request->validate([
            'trx_id' => ['required', 'string','max:255'],
            'phone_number' => ['required', 'string','max:255'],
        ]);

        $trx_id = $request->input('trx_id');
        $phone_number = $request->input('phone_number');
        $details = BookingTransaction::with(['carStore','carService'])->where('trx_id', $trx_id)->where('phone_number', $phone_number)->first();

        if(!$details){
            return redirect()->back()->with('error', 'Transaction not found');
        }
        $ppn = 0.11;
        $totalPpn = $details->carService->price * $ppn;
        $bookingfee = 25000;

        return view('front.transactions_details',compact('details','totalPpn','bookingfee'));
    }

    public function getHotelRooms(Request $request)
    {
        $url = 'https://api-dev.travelprologue.com/api/MSFHotelStaticData/GetHotelRooms';
        $params = [
            'Token' => 'UJYV1L1DF3QRHAXG83PI',
            'TPHotelId' => '657e6dd4a32aa3973e25e3c53aa338648cf50b57',
            'HotelId' => '8090148'
        ];

        $client = new \GuzzleHttp\Client();

        $response = $client->post($url, [
            'form_params' => $params,
        ]);

        $responseBody = json_decode($response->getBody());

        return response()->json($responseBody);
    }

    public function getHotelRoomsTes(Request $request)
    {
        $url = 'https://api-dev.travelprologue.com/api/MSFHotelStaticData/GetHotelRateplans';
        $params = [
            'Token' => 'UJYV1L1DF3QRHAXG83PI',
            "TPHotelId" => "657e6dd4a32aa39ospqjkwjaa338648cf50b57",
            "HotelId" => null
        ];

        $client = new \GuzzleHttp\Client();

        $response = $client->post($url, [
            'form_params' => $params,
        ]);

        $responseBody = json_decode($response->getBody());

        return response()->json($responseBody);
    }


}
