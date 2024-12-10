<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\SerialNo;
use App\Models\City;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use DB;
use App\Models\UserModel;
use Hash;
use Auth;
use Illuminate\Support\Facades\Redirect;
class AccountController extends Controller
{
    
    public $acObj;
    public function __construct()
    {
        $this->acObj = new Account();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $a['title'] = 'Account';
        $a['acgroup'] = AccountGroup::with('child')->where('parent_id','0')->get();
        $a['city'] = City::orderBy('name')->get();
        $a['state'] = State::orderBy('name')->get();
        $a['breadcrumb']=breadcrumb([
                'Accounts' => route('admin.account.index'),
        ]);
        return view('admin.account.index')->with($a);
       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function listing(request $r)
    {
        $columns = array(
            0 =>'id',
            1 =>'name',
            2 =>'phone',
            3 =>'city',
            4 =>'state',
            5 =>'group',
            6 =>'type',
            7 =>'block_status',
            8 =>'openingBalance',
            9 =>'discount_rate',
            10 =>'user_id'
        );

        $totalData = Account::count();
        $totalFiltered = $totalData;
        $limit = $r->input('length');
        $start = $r->input('start');
        $order = $columns[$r->input('order.0.column')];
        $dir = $r->input('order.0.dir');
        if(empty($r->input('search.value')) && empty($r->input('acgroup')) && empty($r->input('actype')) && empty($r->input('acpricegroup')) && empty($r->input('acstatus')) && empty($r->input('accity')) && empty($r->input('acstate')))
        {
            $posts = Account::with('acGroupData','citydata','statedata')->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else
        {
				$posts = Account::with('acGroupData','citydata','statedata');
				$totalFiltered = Account::with('acGroupData');

						//======Filter By Account Status=======
						if($r->input('acstatus')!=''){
							$status=$r->input('acstatus');
							if($status=='d'){
								//-- Inactive-- Account---
								$posts->where('status',0);
								$totalFiltered->where('status','=',0);
							}else if($status==1){
								//-- active-- Account---
								$posts->where('status',1);
								$totalFiltered->where('status',1);
							}else if($status==2){
								//-- Blocked-- Account---
								$posts->where('block_status',1);
								$totalFiltered->where('block_status',1);
							}else{}

						}

						//======Search By input type=======
						if(!empty($r->input('search.value'))){
							$search = $r->input('search.value');


                    $posts->where(function($query) use ($search){
                        $query->where('id','LIKE',"%{$search}%")
                             ->orWhere('name','LIKE',"%{$search}%")
                             ->orWhere('phone','LIKE',"%{$search}%")
                             ->orWhere('city','LIKE',"%{$search}%");
                        })
						->orWhereHas('acGroupData', function ($q) use ($search) { $q->where('name', 'like',"%{$search}%"); });


						$totalFiltered->where(function($query) use ($search){
	                                $query->where('id','LIKE',"%{$search}%")
	                                 ->orWhere('name','LIKE',"%{$search}%")
                                    ->orWhere('phone','LIKE',"%{$search}%")
	                                  ->orWhere('city','LIKE',"%{$search}%");
	                                })
								->orWhereHas('acGroupData', function ($q) use ($search) { $q->where('name', 'like',"%{$search}%"); });

						}

						//======Filter By Account Group=======
						if($r->input('acgroup')>0){
								$posts->where('acGroup',$r->input('acgroup'));
								$totalFiltered->where('acGroup',$r->input('acgroup'));
						}

						//======Filter By Account type=======
						if($r->input('actype')>0){
								$posts->where('type',$r->input('actype'));
								$totalFiltered->where('type',$r->input('actype'));
						}

						//======Filter By Price Group ===========
						if($r->input('acpricegroup')>0){
								$posts->where('priceGroup',$r->input('acpricegroup'));
								$totalFiltered->where('priceGroup',$r->input('acpricegroup'));
						}

                         //======Filter by state =======
						if($r->input('acstate')>0){
                            $posts->where('state_id',$r->input('acstate'));
                            $totalFiltered->where('state_id',$r->input('acstate'));
                        }

                         //======Filter by City =======
						if($r->input('accity')>0){
                            $posts->where('city_id',$r->input('accity'));
                            $totalFiltered->where('city_id',$r->input('accity'));
                        }

            $posts= $posts->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

		    $totalFiltered= $totalFiltered->count();
        }
        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $list)
            {

                    $img = '<h2><i class="nav-icon fa fa-user-o"></i></h2>';
                    if($list->priceGroup==2){
                        $nd['pricegroup'] = 'WSP';
                    }else{
                        $nd['pricegroup'] = 'RSP';
                    }

                    if($list->type==1){
                        $nd['type'] = 'D';
                        $nd['type'].='/'.$nd['pricegroup'];
                    }else if($list->type==2){
                        $nd['type'] = 'W'; 
                        $nd['type'].='/'.$nd['pricegroup'];
                    }else{
                        $nd['type'] = '-';
                    }

                    if($list->acGroup!=4){
                        $nd['type'] = '-';
                    }

                    $nd['user_name']='';
                    if($list->user_id >=1){
                        $usr=UserModel::where('id',$list->user_id)->select('id','name')->first();
                        if(!empty($usr)){
                        $nd['user_name']=$usr->name.'<br><small>'.date('d-M-y H:i',strtotime($list->updated_at)).'</small>';
                        }    
                    }

                $nd['banner']=$img;
                $nd['id'] = $list->id;
                $nd['name'] = '<a href="'.url('admin/party-ledger-book/'.$list->id).'" title="'.$list->name.'">'.Str::limit($list->name, 20, '...').'</a><br><small data-title="Account opening date">'.date('Y-m-d',strtotime($list->created_at)).'</small>';
                $nd['phone'] =$list->phone;
                
                if($list->city_id>0){
                    $nd['city']=ucFirst($list->citydata->name);
                }else{
                    $nd['city'] = '';
                }

                if($list->state_id>0){
                    
                    $nd['state']=ucFirst($list->statedata->name);
                }else{
                    $nd['state'] = '';
                }
                
                if(!empty($list->country) && $list->country!='India'){
                    $nd['state'].=' <small>('.$list->country.')</small>';
                }

    				if($list->block_status==1){
						$vall="'block',".$list->id;
    					$nd['block_status'] = '<span class="badge badge-danger " onclick="blockstatus('.$vall.')" title="'.$list->block_remark.'">Blocked</span><br>';
    				}else{
    					$nd['block_status']='';
    				}

					if($list->status==1){
							$vall="'status',".$list->id;
    			    		$nd['block_status'] .='<span class="text-success" onclick="blockstatus('.$vall.')">Active</span>';
    				}else{
							$vall="'status',".$list->id;
    					$nd['block_status'].='<span class="text-warning" onclick="blockstatus('.$vall.')">Inactive</span>';
    				}

    			$nd['acgroup'] = $list->acGroupData->name;
                $nd['opening'] = $list->openingBalance;
                $nd['disrate'] = $list->discount_rate;
                $nd['action'] =  '<a href="'.route('admin.account.show', $list->id).'" class="text-success">
                                        <i class="fa fa-copy" aria-hidden="true"></i>
                                    </a>
                                    <a href="'.route('admin.account.edit', $list->id).'" class="text-info">
                                        <i class="fa fa-pen" aria-hidden="true"></i>
                                    </a>
                                    <span class="text-danger" type="button" onclick="deleteItem('.$list->id.')">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </span>
                                    <form id="delete-form-'.$list->id.'" action="'.route('admin.account.destroy', $list->id).'" method="post"
                                            style="display:none;">
                                    <input type="hidden" name="_token" value="'.csrf_token().'">
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>';

                /*
                Helper::getButtons([
                                ['key'=>'Edit','link'=>route('pages.add',$list->id)],
                                ['key'=>'View','link'=>route('cms.view',$list->slug)],
                                // ['key'=>'Delete','link'=>$list->id]
                            ]);
                */
                $data[] = $nd;
            }
        }
      //  return $data;
      $json_data = array(
                "draw"            => intval($r->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data
                );
        echo json_encode($json_data);
    }

    public function create()
    {
        $a['title']='Create Account';
        $a['account'] = new Account();
        $a['acgroup'] = AccountGroup::with('child')->where('parent_id','0')->get();
        $a['city']=City::orderBy('name','asc')->get();
        $a['state']=State::orderBy('name','asc')->get();
		$a['nextBill']=getNewSerialNo('account_code');
        $a['breadcrumb']=breadcrumb([
            'Accounts' => route('admin.account.index'),
            ]);
        return view('admin.account.create')->with($a);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $r->except('_token');
        $rules = [
            'acCode'=>'required | unique:tbl_account',
            'name' => 'required | min:3',
            'phone' => 'required',
            'photo' => 'image',
        ];

        $validation = Validator::make($inputs, $rules);
        if ($validation->fails())
        {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $image = $r->file('photo');
        $slug =  Str::slug($r->input('name'));
        if (isset($image))
        {
            $currentDate = Carbon::now()->toDateString();
            $imageName = $slug.'-'.uniqid().'.'.$image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('account'))
            {
                Storage::disk('public')->makeDirectory('account');
            }
            $postImage = Image::make($image)->resize(480, 320)->stream();
            Storage::disk('public')->put('account/'.$imageName, $postImage);
        }
        else
        {
            $imageName = '';
        }
				$code=$this->NewbillNo;

                $account = new Account();
                $account->acCode = $code;
                $account->name = $r->input('name');
                $account->email = $r->input('email');
                $account->phone = $r->input('phone');
                $account->address = $r->input('address');
               // $account->city = $r->input('city');
                $account->city_id = $r->input('city_id');
                $account->state_id = $r->input('state_id');
                $account->country = $r->input('country');
                $account->pinCode = $r->input('pinCode');
                $account->type = $r->input('type');
                $account->acGroup = $r->input('acGroup');
                $account->contactPerson = $r->input('contactPerson');
                $account->account_holder = $r->input('account_holder');
                $account->account_number = $r->input('account_number');
                $account->bank_name = $r->input('bank_name');
                $account->bank_branch = $r->input('bank_branch');
                $account->CSTN_No = $r->input('CSTN_No');
                $account->GSTN_No = $r->input('GSTN_No');
                $account->opening_type = $r->input('opening_type');
                $account->openingBalance = $r->input('openingBalance');
                $account->creditDays = $r->input('creditDays');
                $account->creditAlertDays = $r->input('creditAlertDays');                
                $account->discount_rate=$r->input('discountRate');
				$account->status=$r->input('status');
                $account->block_status=$r->input('blockedStatus');
                $account->block_remark=$r->input('blockedRemark');
                $account->photo = $imageName;
                $account->priceGroup =$r->input('priceGroup');
                $account->referred_by =$r->input('referredBy');
                $account->term_cond =$r->input('tnc');
                $account->transport =$r->input('transport');
                $account->payby =$r->input('payby');
                $account->user_id =Auth::user()->id;
                $account->visit_type =$r->input('visit_type');
                $account->save();

		$sn=SerialNo::where('name','=','account_code')->increment('next_number',1);
		if($account->acGroup==4 && $account->visit_type=='1' ){
		    Toastr::success('Account Successfully Created, Please created Inquery', 'Success!!!');
            return redirect()->route('admin.sale_inquery.create')->with('newAccountId',$account->id);
		}else{
            Toastr::success('Account Successfully Created', 'Success!!!');
            return redirect()->route('admin.account.index');
		}
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $a['title']='Edit Account';
        $a['breadcrumb']=breadcrumb([
            'Accounts' => route('admin.account.index'),
            ]);
        $a['acgroup'] = AccountGroup::with('child')->where('parent_id','0')->get();
        $a['city']=City::orderBy('name','asc')->get();
        $a['state']=State::orderBy('name','asc')->get();
        $a['nextBill']='';
        $a['account']=Account::with('acGroupData','citydata','statedata','updateBy','blockby')->find($id);
        return view('admin.account.create')->with($a);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $r, string $id)
    {
        $account=Account::find($id);
        $inputs = $r->except('_token');
        $rules = [
            'acCode'=>'required',
            'name' => 'required | min:3',
        ];

        $validation = Validator::make($inputs, $rules);
        if ($validation->fails())
        {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $image = $r->file('photo');
        $slug =  Str::slug($r->input('name'));
        if (isset($image))
        {
            $currentDate = Carbon::now()->toDateString();
            $imageName = $slug.'-'.uniqid().'.'.$image->getClientOriginalExtension();
            if(!Storage::disk('public')->exists('account'))
            {
                Storage::disk('public')->makeDirectory('account');
            }

            // delete old photo
            if (Storage::disk('public')->exists('account/'. $account->photo))
            {
                Storage::disk('public')->delete('account/'. $account->photo);
            }

            $postImage = Image::make($image)->resize(480, 320)->stream();
            Storage::disk('public')->put('account/'.$imageName, $postImage);

            $account->photo = $imageName;
        }

        if($account->block_status=='0' && $r->input('blockedStatus')=='1'){
            $account->block_by=Auth::user()->id;
        }
        $account->name = $r->input('name');
        $account->email = $r->input('email');
        $account->phone = $r->input('phone');
        $account->address = $r->input('address');
        //$account->city = $r->input('city');
        //$account->state = $r->input('state');
        $account->city_id = $r->input('city_id');
        $account->state_id = $r->input('state_id');
        $account->country = $r->input('country');
        $account->pinCode = $r->input('pinCode');
        $account->type = $r->input('type');
        $account->acGroup = $r->input('acGroup');
        $account->contactPerson = $r->input('contactPerson');
        $account->account_holder = $r->input('account_holder');
        $account->account_number = $r->input('account_number');
        $account->bank_name = $r->input('bank_name');
        $account->bank_branch = $r->input('bank_branch');
        $account->CSTN_No = $r->input('CSTN_No');
        $account->GSTN_No = $r->input('GSTN_No');
        $account->openingBalance = $r->input('openingBalance');
        $account->opening_type = $r->input('opening_type');
        $account->creditDays = $r->input('creditDays');
        $account->creditAlertDays = $r->input('creditAlertDays');  
        $account->discount_rate = $r->input('discountRate');
        $account->status= $r->input('status');
        $account->block_status=$r->input('blockedStatus');
        $account->block_remark=$r->input('blockedRemark');
        $account->overdue_amount=$r->input('old_overdue');
        $account->priceGroup=$r->input('priceGroup');
        $account->referred_by =$r->input('referredBy');
        $account->term_cond =$r->input('tnc');        
        $account->transport =$r->input('transport');
        $account->payby =$r->input('payby');        
        $account->user_id =Auth::user()->id;
        $account->visit_type =$r->input('visit_type');
        $account->save();

				//====Account Login Allowed======
				$LoginUser=UserModel::where('account_id',$account->id)->count();
				 if($r->input('allowLogin')=='Y' && $LoginUser==0 && ($r->input('acGroup')=='3' || $r->input('acGroup')=='4'))
				 {
					 $ac=Account::where('id',$account->id)->update(['allow_login'=>'Y']);

					 if($r->input('acGroup')=='3'){
						 $accountType='103';
					 }else{
						 $accountType='104';
					 }
					$user = new UserModel();
	                $user->name = strtolower($r->input('name'));
					 if(!empty($r->input('email'))){
						 $user->email = strtolower($r->input('email'));
					 }else{
						 $user->email = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $r->input('name'))).$account->id.'@ab.com';
					 }

                    $user->phone = $r->input('phone');
                    $user->address = $r->input('address');
                    $user->password = Hash::make($r->input('phone'));
                    $user->user_type = $accountType;
					$user->is_active = '1';
					$user->account_id = $account->id;
	                $user->save();
				 }else if($r->input('allowLogin')=='Y' && $LoginUser==1){
                        UserModel::where('account_id',$account->id)->update(['is_active'=>'1']);
					    Account::where('id',$account->id)->update(['allow_login'=>'Y']);
				 }else if($r->input('allowLogin')=='N' && $LoginUser==1){
					    UserModel::where('account_id',$account->id)->update(['is_active'=>'0']);
					    Account::where('id',$account->id)->update(['allow_login'=>'N']);
				 }else{

				 }

        Toastr::success('Account Successfully Updated', 'Success!!!');
      //  return redirect()->route('admin.account.index');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
