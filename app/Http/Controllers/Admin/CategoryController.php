<?php

namespace App\Http\Controllers\Admin;

use App\CategoryModel;
use App\ParentCatModel;
use App\CategoryMapModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\WebController;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends WebController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $category_obj, $parent_cat_obj, $category_map_model;
    public function __construct()
    {
        $this->category_obj = new CategoryModel();
        $this->parent_cat_obj =  new ParentCatModel();
        $this->category_map_model = new CategoryMapModel();
    }

    public function index()
    {
        return view('admin.category.index', [
            'title' => 'Parent Category',
            'breadcrumb' => breadcrumb([
                'Category' => route('admin.category.index'),
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
        $parent_category = $this->category_obj->orderBy('name')->where('status', 'active')->get();
        $select_cat = [];
        $mastercat = $this->parent_cat_obj;
        return view('admin.category.create', [
            'title' => "Add Parent Category",
            'category' => $parent_category,
            'mastercat' => $mastercat,
            'select_cat' =>$select_cat,
            'breadcrumb' => breadcrumb([
                'Category' => route('admin.category.index')
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
            'mastercatName' => ['required', 'max:255'],
            'mastercatDesc' => ['required']
        ]);
        $request_data = $request->all();
        $request_data['name'] = $request->mastercatName;
        $request_data['description'] = $request->mastercatDesc;
        unset($request_data['mastercatName']);
        unset($request_data['mastercatDesc']);
        unset($request_data['categories']);
        $categories = $this->parent_cat_obj->saveCategory($request_data);
        if ($categories->save()) {
            if (!empty($request->input('categories'))) {
                CategoryMapModel::where('parent_id', $categories->id)->delete();
                foreach ($request->input('categories') as $catID) {
                    $map = new CategoryMapModel();
                    $map->parent_id = $categories->id;
                    $map->category_id = $catID;
                    $map->save();
                }
                success_session('Parenet Category updated successfully');
            } else {
                success_session('Parent Category successfully Update without Child categories');
            }
        } else {
            error_session('Unable to update data');
        }
        return redirect()->route('admin.category.index');
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parent_category = $this->category_obj->orderBy('name')->where('status', 'active')->get();
        $select_cat = $this->category_map_model->where('parent_id', $id)->pluck('category_id')->toArray();
        $data = $this->parent_cat_obj->find($id);
        if (isset($data) && !empty($data)) {
            return view('admin.category.create', [
                'title' => 'Category Update',
                'category' => $parent_category,
                'select_cat' => $select_cat,
                'breadcrumb' => breadcrumb([
                    'Category' => route('admin.category.index'),
                    'edit' => route('admin.category.edit', $id),
                ]),
            ])->with(compact('data'));
        }
        return redirect()->route('admin.category.index');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $request->validate([
            'mastercatName' => ['required', 'max:255'],
            'mastercatDesc' => ['required']
        ]);
        if ($id != null) {
            $mct = ParentCatModel::find($id);
            $mct->name = $request->mastercatName;
            $mct->description = $request->mastercatDesc;
            if ($mct->save()) {
                if (!empty($request->input('categories'))) {
                    CategoryMapModel::where('parent_id', $mct->id)->delete();
                    foreach ($request->input('categories') as $catID) {
                        $map = new CategoryMapModel();
                        $map->parent_id = $mct->id;
                        $map->category_id = $catID;
                        $map->save();
                    }
                    success_session('Parenet Category updated successfully');
                } else {
                    success_session('Parent Category successfully Update without Child categories');
                }
            } else {
                error_session('Unable to update data', 'Danger');
            }
            return redirect()->route('admin.category.index');
        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->parent_cat_obj::where('id', $id)->first();
        if ($data) {
            $data->delete();
            success_session('Parenet Category deleted successfully');
        } else {
            error_session('Parenet Category not found');
        }
        return redirect()->route('admin.category.index');
    }

    public function listing(Request $request)
    {
        $data = $this->parent_cat_obj::all();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $param = [
                    'id' => $row->id,
                    'url' => [
                        'status' => route('admin.category.status_update', $row->id),
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
                        'delete' => route('admin.category.destroy', $row->id),
                        'edit' => route('admin.category.edit', $row->id),
                        // 'view' => route('admin.news.show', $row->id),
                    ]
                ];
                return $this->generate_actions_buttons($param);
            })
            ->rawColumns(["status", "action"])
            ->make(true);
    }
}
