<?php

namespace App\Http\Controllers\Home;

use App\Models\BenDocs;
use App\Models\BenEntry;
use App\Models\District;
use App\Models\GP;
use App\Models\Scheme;
use App\Models\Taluka;
use App\Models\UrbanBody;
use App\Models\Ward;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BeneficiarySearchController extends Controller
{
    public static function index(Request $request)
    {
        $districts = District::get();
        $schemes = Scheme::where('is_active', 1)->orderBy('id')->get();
        return view('home.search-b', [
            'districts' => $districts,
            'schemes' => $schemes
        ]);
    }
    public function search(Request $request)
    {
        try {

            $client = app('elasticsearch');

            $searchType = $request->input('search_type');
            $searchValue = trim($request->input('search_value') ?? '');

            $district = $request->input('district');
            $urbanRural = $request->input('urbanRural');
            $block = $request->input('block');
            $muncid = $request->input('muncid');
            $gp_ward = $request->input('gp_ward');
            $scheme_id = $request->input('scheme');


            //----------------------------------------------------------------------
            // BUILD FILTER ARRAY SAFELY
            //----------------------------------------------------------------------
            $filters = [];

            if (!empty($gp_ward)) {

                $filters[] = ['term' => ['created_by_dist_code' => $district]];
                $filters[] = ['term' => ['block_ulb_code' => $muncid]];
                $filters[] = ['term' => ['gp_ward_code' => $gp_ward]];

            } elseif (!empty($muncid)) {

                $filters[] = ['term' => ['created_by_dist_code' => $district]];
                $filters[] = ['term' => ['block_ulb_code' => $muncid]];

            } elseif (!empty($block)) {

                $filters[] = ['term' => ['created_by_dist_code' => $district]];
                $filters[] = ['term' => ['created_by_local_body_code' => $block]];

            } elseif (!empty($district)) {

                $filters[] = ['term' => ['created_by_dist_code' => $district]];
            }

            if (!empty($scheme_id)) {
                $filters[] = ['term' => ['scheme_id' => $scheme_id]];
            }

            if (!empty($urbanRural)) {
                $filters[] = ['term' => ['rural_urban_id' => $urbanRural]];
            }


            //----------------------------------------------------------------------
            // SAFELY BUILD QUERY
            //----------------------------------------------------------------------

            $must = [];

            // SEARCH TYPE HANDLING
            if (in_array($searchType, ['aadhar_no', 'mobile_no', 'ben_id', 'bank_account'])) {

                // Case: Exact match searches — requires searchValue NOT empty
                if ($searchValue !== '') {
                    $must[] = ['term' => [$searchType => $searchValue]];
                }

            } else {
                // Case: Full text search — ADD ONLY IF searchValue NOT EMPTY
                if ($searchValue !== '') {
                    $must[] = [
                        'multi_match' => [
                            'query' => $searchValue,
                            'fields' => [
                                'full_name^4',
                                'ben_fname^3',
                                'ben_lname^3',
                                'ben_mname',
                                'aadhar_no^5',
                                'mobile_no'
                            ]

                        ]
                    ];
                }
            }


            // ELASTICSEARCH DOES NOT ALLOW EMPTY must[]
            if (empty($must)) {
                // fallback to match_all + filters
                $must[] = ['match_all' => (object) []];
            }



            //----------------------------------------------------------------------
            // FINAL QUERY BODY
            //----------------------------------------------------------------------
            $query = [
                'bool' => [
                    'must' => $must,
                    'filter' => $filters
                ]
            ];

            $params = [
                'index' => 'beneficiaries',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => $must,
                            'filter' => $filters,
                            'should' => array_filter([
                                !empty($district) ? ['term' => ['created_by_dist_code' => ['value' => $district, 'boost' => 5]]] : null,
                                !empty($block) ? ['term' => ['created_by_local_body_code' => ['value' => $block, 'boost' => 4]]] : null,
                                !empty($muncid) ? ['term' => ['block_ulb_code' => ['value' => $muncid, 'boost' => 3]]] : null,
                                !empty($gp_ward) ? ['term' => ['gp_ward_code' => ['value' => $gp_ward, 'boost' => 2]]] : null,
                            ])
                        ]
                    ],

                    'highlight' => [
                        'fields' => [
                            'full_name' => new \stdClass(),
                            'ben_fname' => new \stdClass(),
                            'ben_lname' => new \stdClass(),
                        ],
                        'pre_tags' => ['<mark><b>'],
                        'post_tags' => ['</b></mark>']
                    ],

                    'from' => $request->input('from', 0),
                    'size' => $request->input('size', 100),
                    'sort' => [
                        ['ben_id' => ['order' => 'desc']]
                    ]
                ]
            ];


            // Execute query
            $results = $client->search($params)->asArray();

            return response()->json($results['hits']['hits']);
        } catch (\Exception $e) {
            dd($e);
        }

    }


    public function ben_details(Request $request, $id)
    {
        // dd($id);
        $view_type = $request->view_type ?? null;
        if (empty($id)) {
            return redirect("/")->with('danger', 'Applicant ID Not Found');
        }
        if (!is_numeric($id)) {
            return redirect("/")->with('danger', 'Applicant ID Not Valid');
        }




        $id = $request->id;
        $condition_arr = array();
        $condition_arr['id'] = $id;


        $row = BenEntry::where($condition_arr)->first();
        // dd($row);
        if (empty($row)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }

        $is_state_login = 0;
        $district_state_name = '';
        $urban_code_state_name = '';
        $block_subdiv_state_name = '';



        $docs = BenDocs::where('beneficiary_id', $id)->orderBy('document_type')->get();

        if ($row->dist_code != "") {
            $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
            $district_name = $district->district_name;
        }
        $block_name = "";
        if ($row->block_ulb_code != "") {
            if ($row->rural_urban_id == 1) {
                $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
                if (!empty($block)) {
                    $block_name = $block->urban_body_name;
                }
            } else {
                if (!empty($row->block_ulb_code)) {
                    $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
                    if (!empty($block)) {
                        $block_name = $block->block_name;
                    } else {
                        $block_name = '';
                    }
                } else {
                    $block_name = '';
                }
            }
        }
        $row->block_name = $block_name;
        $gp_name = "";
        if ($row->gp_ward_code != "") {
            if ($row->rural_urban_id == 1) {
                $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
                if (!empty($gp_ward)) {
                    $gp_name = $gp_ward->urban_body_ward_name;
                }
            } else {
                $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
                if (!empty($gp)) {
                    $gp_name = $gp->gram_panchyat_name;
                }
            }
        }
        $row->gp_name = $gp_name;




        $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
        $doc_profile_image_id = 999;
        if ($doc_profile_image) {
            $doc_profile_image_id = $doc_profile_image->id;
        }
        $scheme_capacity_arr = array();
        $scheme_capacity_arr['visible'] = 0;
        $is_dup_msg = array();

        //dump($is_verifier);dump( $view_type);die;



        $is_hod = 0;
        $rejectBtnvisible = 1;

        $scheme_id = $row->scheme_id;

        //dd($view_type);
        return view('home.ben-details', [
            'is_state_login' => $is_state_login,
            'district_state_name' => $district_state_name,
            'block_subdiv_state_name' => $block_subdiv_state_name,

            'scheme_capacity_arr' => $scheme_capacity_arr,
            'row' => $row,
            'district_name' => $district_name,
            'block_name' => $block_name,
            'gp_name' => $gp_name,
            'docs' => $docs,
            'image_id' => $doc_profile_image_id,

            'is_dup_msg' => $is_dup_msg,
            'scheme_id' => $scheme_id,

            'is_hod' => $is_hod,
            'view_type' => $view_type,
            'rejectBtnvisible' => $rejectBtnvisible,
        ]);

    }
    function viewEncloser(Request $request)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        try {
            $created_by_dist_code = $request->created_by_dist_code;
            $beneficiary_id = $request->beneficiary_id;
            $doc_type_id = $request->document_type;
            $scheme_id = $request->scheme_id;
            if (empty($created_by_dist_code)) {
                $return_text = 'District Parameter Not Valid';
                //return redirect("/")->with('error',  $return_text);
            }
            if (!ctype_digit($created_by_dist_code)) {
                $return_text = 'District Not Valid';
                //return redirect("/")->with('error',  $return_text);
            }
            if (empty($scheme_id)) {
                $return_text = 'Scheme Parameter Not Valid';
                //return redirect("/")->with('error',  $return_text);
            }
            if (!ctype_digit($scheme_id)) {
                $return_text = 'Scheme Id Not Valid';
                // return redirect("/")->with('error',  $return_text);
            }
            if (empty($beneficiary_id)) {
                $return_text = 'Beneficiary Id Parameter Not Valid';
                //return redirect("/")->with('error',  $return_text);
            }
            if (!ctype_digit($beneficiary_id)) {
                $return_text = 'Beneficiary Id Not Valid';
                //return redirect("/")->with('error',  $return_text);
            }
            if (empty($doc_type_id)) {
                $return_text = 'Doc Type Id Parameter Not Valid';
                //  return redirect("/")->with('error',  $return_text);
            }
            if (!ctype_digit($doc_type_id)) {
                $return_text = 'Doc Type Id Not Valid';
                // return redirect("/")->with('error',  $return_text);
            }
            //dd($return_text);

            $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
            if (empty($scheme_obj)) {
                $return_text = 'Scheme Not Valid';
                return redirect("/")->with('error', $return_text);
            }

            if (!empty($scheme_obj->short_code)) {
                $schema = $scheme_obj->short_code;
            } else {
                $schema = "pension";
            }
            $condition = array();
            $condition['beneficiary_id'] = $beneficiary_id;
            $condition['created_by_dist_code'] = $created_by_dist_code;
            $condition['document_type'] = $doc_type_id;

            $doc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where($condition)->first();
            //dd($doc);

            if (!empty($doc)) {

                $mime_type = $doc->document_mime_type;
                $file_extension = $doc->document_extension;
                if ($file_extension != 'png' && $file_extension != 'jpg' && $file_extension != 'jpeg' && $file_extension != 'pdf') {
                    if ($mime_type == 'image/png') {
                        $file_extension = 'png';
                    } else if ($mime_type == 'image/jpeg') {
                        $file_extension = 'jpg';
                    } else if ($mime_type == 'application/pdf') {
                        $file_extension = 'pdf';
                    }
                }
                try {
                    if (strtoupper($file_extension) == 'PNG' || strtoupper($file_extension) == 'JPG' || strtoupper($file_extension) == 'JPEG') {
                        $resultimg = str_replace("data:image/" . $file_extension . ";base64,", "", $doc->attched_document);
                        //dd($resultimg);
                        $file_name = $doc->document_type . '_' . $doc->beneficiary_id;

                        header('Content-Disposition: attachment;filename="' . $file_name . '.' . $file_extension . '"');
                        header('Content-Type: ' . $mime_type);
                        ob_clean();
                        echo base64_decode($resultimg);
                    } else if (strtoupper($file_extension) == 'PDF') {
                        $decoded = base64_decode($doc->attched_document);
                        $file_name = $doc->document_type . '_' . $doc->beneficiary_id . '.pdf';
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment; filename=' . $file_name);
                        header('Content-Transfer-Encoding: binary');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . strlen($decoded));
                        ob_clean();
                        flush();
                        echo $decoded;
                        exit;
                    }
                } catch (\Exception $e) {
                    $return_text = 'Some error. please try again.';
                    return redirect("/")->with('error', $return_text);
                }

            } else {
                $return_text = 'File Not Found';
                return redirect("/")->with('error', $return_text);
            }
        } catch (\Exception $e) {
            dd($e);
            //return redirect("/")->with('error',  'Some error.please try again ......');
        }



    }





}