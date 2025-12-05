<?php

namespace App\Http\Controllers;

use App\Interfaces\JNMPAuthenticationInterface;
use App\Models\BeneficiaryPersonal;
use App\Models\FaultyBeneficiaryPersonal;
use App\Models\JnmpData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class JnpmController extends Controller
{
    protected $JnmpAuthenticationService;

    public function __construct(JnmpAuthenticationInterface $JnmpAuthenticationService)
    {
        //   dd($jnmpService);
        $this->JnmpAuthenticationService = $JnmpAuthenticationService;
    }

    public function pullJnmpData(Request $request)
    {
        // dd('ok');
        $inserted = null;

        if ($request->isMethod('post')) {

            $rules = [
                'from_date' => 'required|date',
                'to_date'   => 'required|date|after_or_equal:from_date',
                'index'     => 'required|integer|min:1',
                'page_size' => 'required|integer|min:1|max:500'
            ];

            $messages = [
                'from_date.*' => 'Please select a valid start date.',
                'to_date.*'   => 'Please select a valid end date.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            try {

                $payload = [
                    'from_date' => $request->from_date,
                    'to_date'   => $request->to_date,
                    'index'     => $request->index,
                    'page_size' => $request->page_size
                ];

                $response = $this->JnmpAuthenticationService->getJnmpData($payload);

                $data = $response->getData(true);


                if (($data['status'] ?? 500) == 200) {

                    DB::commit();

                    session()->flash('success', $data['message']);
                    return redirect()
                        ->route('jnmp.pull', ['inserted' => $data['inserted']])
                        ->withInput();
                }

                DB::rollBack();
                session()->flash('error', 'Failed to import JNMP data.');
                return redirect()->back();
            } catch (\Exception $e) {

                DB::rollBack();
                session()->flash('error', 'Error: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }

        $header = 'Importing data from Jonmo Mrityu Tothyo portal';
        return view('jnmp.list', compact('header', 'inserted'));
    }

    public function detailsCallback(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'limit' => 'required|integer|min:1|max:500'
        ], [
            'limit.required' => 'Please enter a limit value.',
            'limit.integer'  => 'Limit must be a number.',
        ]);

        try {

            $response = $this->JnmpAuthenticationService->detailsCallBack($request);
            // dd($response);
            $data = $response->getData(true);

            // If callback successful
            if (($data['status'] ?? 0) == 1) {
                session()->flash('success', $data['message']);
            } else {
                session()->flash('error', $data['message']);
            }

            return redirect()->back();
        } catch (\Exception $e) {

            session()->flash('error', 'System Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function totalJnmp(Request $request)
    {

       
    }
}
