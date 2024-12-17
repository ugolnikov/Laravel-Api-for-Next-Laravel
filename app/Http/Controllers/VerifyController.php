<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VerifyController extends Controller
{
    public function validateInn(Request $request)
    {
        $innLength = strlen($request->inn);
        $request->validate([
            'inn' => [
                'required',
                'string',
                'regex:/^\d+$/',
                function ($attribute, $value, $fail) use ($innLength) {
                    if ($innLength == 10) {
                        return;
                    } elseif ($innLength == 12) {
                        return;
                    }
                    $fail('ИНН должен быть 10 знаков для организации или 12 знаков для ИП/физического лица.');
                }
            ],
        ]);
        $existingSeller = Seller::where('inn', $request->inn)->first();

        if ($existingSeller) {
            return response()->json(['error' => 'Продавец с таким ИНН уже существует.'], 409);
        }
        $token = env('DADATA_API_KEY');
        $dadata = new \Dadata\DadataClient($token, null);

        try {
            $result = $dadata->findById("party", $request->inn, 1, ["branch_type" => "MAIN"]);
            if (empty($result)) {
                return response()->json(['error' => 'Юрлицо с таким ИНН не найдено.'], 404);
            }
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка при обращении к DaData: ' . $e->getMessage()], 500);

        }
    }
    public function validateSeller(Request $request)
    {
        $innLength = strlen($request->inn);
        $validator = Validator::make($request->all(), [
            'companyName' => 'required|string|max:255',
            'inn' => [
                'required',
                'string',
                'regex:/^\d+$/',
                function ($attribute, $value, $fail) use ($innLength) {
                    if ($innLength == 10) {
                        return;
                    } elseif ($innLength == 12) {
                        return;
                    }
                    $fail('ИНН должен быть 10 знаков для организации или 12 знаков для ИП/физического лица.');
                }
            ],
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'logo' => 'string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Ошибка валидации данных.',
                'messages' => $validator->errors()
            ], 400);
        }

//        $seller = Seller::where('inn', $request->inn)->first();
        $seller = Auth::guard('sell')->user();
        if (!$seller) {
            return response()->json([
                'error' => 'Компания с таким ИНН не найдена.',
            ], 404);
        }

        $seller->update([
            'company_name' => $request->companyName,
            'inn' => $request->inn,
            'address' => $request->address,
            'phone' => $request->phone,
            'logo' => $request->logo,
            'updated_at' => now()
        ]);
        $seller->update(['is_verify' => true]);

        return response()->json([
            'message' => 'Профиль продавца подтвержден успешно.',
            'data' => $seller,
        ]);
    }
}
