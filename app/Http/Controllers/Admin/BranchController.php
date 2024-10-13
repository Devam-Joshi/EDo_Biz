<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Branch;
use App\Http\Controllers\WebController;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class BranchController extends WebController
{

    public $branch_obj;
    public function __construct()
    {
        $this->branch_obj = new Branch();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.branch.index', [
            'title' => 'Branch',
            'breadcrumb' => breadcrumb([
                'Branch' => route('admin.branch.index'),
            ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.branch.create', [
            'title' => "Add Branch",
            'breadcrumb' => breadcrumb([
                'Branch' => route('admin.branch.index')
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'address'=>['required']
        ]);
        $return_data = $request->all();  
        $branch = $this->branch_obj->saveBranch($return_data);
        if (isset($branch) && !empty($branch)) {
            success_session('Branch created successfully');
        } else {
            error_session('Branch not created');
        }
        return redirect()->route('admin.branch.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function status_update($id = 0)
    {
        $data = ['status' => 0, 'message' => 'Branch Not Found'];
        $find = $this->branch_obj::find($id);
        if ($find) {
            $find->update(['status' => ($find->status == "inactive") ? "active" : "inactive"]);
            $data['status'] = 1;
            $data['message'] = 'Branch status updated';
        }
        return $data;
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->branch_obj->find($id);
        if (isset($data) && !empty($data)) {
            return view('admin.branch.create', [
                'title' => 'Branch Update',
                'breadcrumb' => breadcrumb([
                    'Branch' => route('admin.branch.index'),
                    'edit' => route('admin.branch.edit', $id),
                ]),
            ])->with(compact('data'));
        }
        return redirect()->route('admin.branch.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'address' => ['required']
        ]);
        $branch = $this->branch_obj->find($id);
        if(isset($branch) && !empty($branch)){
            $return_data = $request->all();
            // $return_data['branch'] = !empty($request->branch) ?  $request->branch : 0;
            $this->branch_obj->saveBranch($return_data,$id,$branch);
            success_session('Branch updated successfully');
        }
        else{
            error_session('Branch not found');
        }
        return redirect()->route('admin.branch.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->branch_obj::where('id', $id)->first();
        if ($data) {
            $data->delete();
            success_session('branch deleted successfully');
        } else {
            error_session('branch not found');
        }
        return redirect()->route('admin.branch.index');
    }


    public function listing(Request $request)
    {
        $data = $this->branch_obj::all();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $param = [
                    'id' => $row->id,
                    'url' => [
                        'status' => route('admin.branch.status_update', $row->id),
                    ],
                    'checked' => ($row->status == 'active') ? 'checked' : ''
                ];
                return  $this->generate_switch($param);
            })
            ->addColumn('description', function ($row) {
                return "-";
            })
            ->addColumn('action', function ($row) {
                $param = [
                    'id' => $row->id,
                    'url' => [
                        'delete' => route('admin.branch.destroy', $row->id),
                        'edit' => route('admin.branch.edit', $row->id),
                        // 'view' => route('admin.news.show', $row->id),
                    ]
                ];
                return $this->generate_actions_buttons($param);
            })
            ->rawColumns(["status", "action"])
            ->make(true);
    }
}
