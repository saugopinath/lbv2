<?php

namespace App\Http\Controllers\Api;

use App\Helpers\BSKJwtValid;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use App\Models\BackFromJb;
use App\Models\Block;
use App\Models\BSKUserDutyMapping;
use App\Models\Codemaster;
use App\Models\District;
use App\Models\Subdivision;
use App\Services\BSKJwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BskController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'Invalid credentials',
                    'is_success' => false,
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not create token',
                'is_success' => false,
            ], 500);
        }
        return response()->json([
            'token' => $token,
            'is_success' => true,
        ], 200);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'message' => 'Logged out successfully',
                'is_logout' => true,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Failed to logout',
                'is_logout' => false,
            ], 500);
        }
    }

    public function bskEntryAuthCheck(): JsonResponse|RedirectResponse
    {
        try {
            //             $token = (string) JWTAuth::getToken();
            //             // dd(BSKJwtValid::is_jwt_valid($token));
            //             /** JWT validation */
            //             if (!BSKJwtValid::is_jwt_valid($token)) {
            //                 return response()->json([
            //                     'message' => 'Token is invalid',
            //                     'status'  => 0
            //                 ], 401);
            //             }
            // // dd($token);
            //             /** Read payload */
            //             $payload =JWTAuth::getPayload($token)->toArray();

            $token = (string) JWTAuth::getToken();

            if (!BSKJwtValid::is_jwt_valid($token)) {
                return response()->json(['message' => 'Invalid token'], 401);
            }

            // payload manually decode
            $parts = explode('.', $token);
            $payload = json_decode(
                base64_decode(strtr($parts[1], '-_', '+/'))
            );


            // dd($payload);
            $ticketNo = trim($payload->Ticketno ?? '');

            if (!$ticketNo) {
                return response()->json([
                    'message' => 'Ticket number missing',
                    'status'  => 0
                ], 400);
            }

            /** Prevent duplicate ticket */
            if (BSKUserDutyMapping::where('ticket_no', $ticketNo)->exists()) {
                return response()->json([
                    'message' => 'Token already exists',
                    'status'  => 0
                ], 409);
            }

            /** Validate District */
            $district = District::where('lgd_code', $payload->district_code)->first();
            // dd($district);
            if (!$district) {
                return response()->json([
                    'message' => 'District code is invalid',
                    'status'  => 0
                ], 400);
            }

            /** Rural / Urban validation */
            if ($payload->is_rural === 'Y') {
                $blockExists = Block::where('district_id', $payload->district_code)
                    ->where('lgd_code', $payload->block_code)
                    ->exists();

                if (!$blockExists) {
                    return response()->json([
                        'message' => 'Block code is invalid',
                        'status'  => 0
                    ], 400);
                }
            } else {
                $subDivExists = Subdivision::where('district_id', $payload->district_code)
                    ->where('id', $payload->subdiv_code)
                    ->exists();

                if (!$subDivExists) {
                    return response()->json([
                        'message' => 'Sub division code is invalid',
                        'status'  => 0
                    ], 400);
                }
            }

            /** Save duty mapping */
            BSKUserDutyMapping::create([
                'name'            => trim($payload->name ?? ''),
                'mobile_no'       => trim($payload->mobile_no ?? ''),
                'email'           => trim($payload->email ?? ''),
                'bsk_name'        => trim($payload->bsk_name ?? ''),
                'bsk_code'        => trim($payload->bsk_code ?? ''),
                'district_id'   => $payload->district_code,
                'district_name'   => trim($payload->district_name ?? ''),
                'is_rural'        => $payload->is_rural,
                'agent_id'        => $payload->AgentId,
                'id_from_bsk'     => $payload->id,
                'username'        => trim($payload->userName ?? ''),
                'ticket_no'       => $ticketNo,
                'ohr_code'        => $payload->ohr_code ?? null,
                'deo_code'        => $payload->deo_code ?? null,
                'sub_division_id' => $payload->subdiv_code ?? null,
                'sub_district_name' => $payload->subdiv_name ?? null,
                'block_id'        => $payload->block_code ?? null,
                'block_name'        => $payload->block_name ?? null,
            ]);
            // dd('ok');
            /** Session payload */
            $sessionPayload = [
                'user_id'     => $payload->AgentId,
                'mobile_no'   => $payload->mobile_no,
                'district'    => $payload->district_code,
                'is_rural'    => $payload->is_rural,
                'sub_division' => $payload->subdiv_code ?? null,
                'block'       => $payload->block_code ?? null,
                'ticketNo'    => $ticketNo,
            ];
            // dd($sessionPayload);
            $encryptedTicket = encrypt($sessionPayload);

return redirect()->to(
    url('/api/bskUserSessionCreate') . '?id=' . urlencode($encryptedTicket)
);

            // $encryptedTicket = base64_encode(encrypt($sessionPayload));

            /** Correct redirect */
            // return redirect()->route('bsk.session.create', [
            //     'id' => $encryptedTicket
            // ]);
// return redirect()->to(
//     url('/bskUserSessionCreate') . '?id=' . urlencode($encryptedTicket)
// );

            // return redirect('bskUserSessionCreate?id=' . $encryptedTicket);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status'  => 0
            ], 500);
        }
    }

    public function bskUserSessionCreate(Request $request)
    {
        //  dd($request->query('id'));

    // $sessionData = decrypt($request->query('id'));
    // dd($sessionData);

        // dd('ok');
        try {
            if (!$request->filled('id')) {
                return redirect('api/bsk-entry-done')
                    ->with(['error' => 'Invalid Session Token', 'status' => 0]);
            }

            // Decode & decrypt session payload
            $sessionData = decrypt(base64_decode($request->id));

            if (!isset($sessionData['ticketNo'])) {
                return redirect('api/bsk-entry-done')
                    ->with(['error' => 'Invalid Session Data', 'status' => 0]);
            }

            // ORM lookup (NO RAW DB)
            $duty = BSKUserDutyMapping::with('user')
                ->where('ticket_no', $sessionData['ticketNo'])
                ->first();

            if (!$duty || !$duty->user) {
                return redirect('api/bsk-entry-done')
                    ->with(['error' => 'User not found', 'status' => 0]);
            }

            // Auth Login (Laravel 11)
            Auth::login($duty->user);

            // Store session for Livewire
            session([
                'bskrole' => [
                    'user_id'         => $duty->agent_id,
                    'ticket_no'       => $duty->ticket_no,
                    'district_id'     => $duty->district_id,
                    'is_rural'        => $duty->is_rural,
                    'sub_division_id' => $duty->sub_division_id,
                    'block_id'        => $duty->block_id,
                ]
            ]);

            return redirect()->route('formEntryOption');
        } catch (\Throwable $e) {

            return redirect('api/bsk-entry-done')->with([
                'error'  => 'Something went wrong',
                'status' => 0
            ]);
        }
    }

    public function formEntryOption(Request $request)
    {
        return view(
            'lb_bsk/entryOption'
        );
    }
}
