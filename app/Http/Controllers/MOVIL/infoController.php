<?php

namespace App\Http\Controllers\Movil;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InfoController extends Controller
{
    public function hotelIndex(Request $request)
    {
        try{
            $hotel = Hotel::first();
            return response()->json($hotel, 200);
            
        }catch(\Exception $e){
            Log::error($e->getMessage());
        }
        catch (\PDOException $e) {
            Log::error($e->getMessage());
        }

       
    }
}
