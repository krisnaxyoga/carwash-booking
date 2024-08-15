<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\CarService;
use App\Models\CarStore;

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

        return view('front.search',['carServices'=>$services,'store'=>$carStore,'city_name'=>$city ? $city->name : 'unknown']); 
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
