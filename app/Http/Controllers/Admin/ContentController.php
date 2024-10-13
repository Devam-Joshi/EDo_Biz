<?php

namespace App\Http\Controllers\Admin;

use App\Content;
use App\Http\Controllers\WebController;
use Illuminate\Http\Request;


class ContentController extends WebController
{


    public function index()
    {
        $title = 'Content';
        return view('admin.content.index', [
            'title' => $title,
            'breadcrumb' => breadcrumb([
                $title => route('admin.content.index'),
            ]),
        ]);
    }

    public function listing()
    {
        $datatable_filter = datatable_filters();
        $offset = $datatable_filter['offset'];
        $search = $datatable_filter['search'];
        $return_data = array(
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0
        );
        $main = Content::query();
        $return_data['recordsTotal'] = $main->count();
        if (!empty($search)) {
            $main->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%");
            });
        }
        $return_data['recordsFiltered'] = $main->count();
        $all_data = $main->orderBy($datatable_filter['sort'], $datatable_filter['order'])
            ->offset($offset)
            ->limit($datatable_filter['limit'])
            ->get();
        if (!empty($all_data)) {
            foreach ($all_data as $key => $value) {
                $param = [
                    'id' => $value->id,
                    'url' => [
                        'edit' => route('admin.content.edit', $value->id),
                    ],
                    'checked' => ($value->status == 'active') ? 'checked' : ''
                ];
                $return_data['data'][] = array(
                    'id' => $offset + $key + 1,
                    'title' => $value->title,
                    'action' => $this->generate_actions_buttons($param),
                );
            }
        }
        return $return_data;
    }

    public function edit($id)
    {
        $data = Content::find($id);
        if ($data) {
            $title = "edit Content";
            return view('admin.content.edit', [
                'title' => $title,
                'data' => $data,
                'breadcrumb' => breadcrumb([
                    $title => route('admin.content.index'),
                    'edit' => route('admin.content.edit', $data->id)
                ]),
            ]);
        }
        error_session('Content not found');
        return redirect()->route('admin.content.index');
    }

    public function update(Request $request, $id)
    {

        $data = Content::find($id);
        if ($data) {
            $inputs = $request->validate([
                'title' => ['required', "max:255"],
                'content' => ['required'],
            ]);
            $data->update($inputs);
            success_session('content updated successfully');
        } else {
            error_session('content not found');
        }
        return redirect()->route('admin.content.index');
    }

}
