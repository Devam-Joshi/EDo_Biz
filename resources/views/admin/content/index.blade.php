@extends('layouts.master')
@section('title')
    @lang('translation.Data_Tables')
@endsection
@section('css')

    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/admin/vendors/general/datatable/jquery.dataTables.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
@endsection
@section('content')

    @include('components.breadcum')
    <div class="row">
        <div class="col-12">
            {!! success_error_view_generator() !!}

        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive ">
                    <table id="listResults" class="table dt-responsive mb-4  nowrap w-100 mb-">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>title</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('/assets/admin/vendors/general/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('/assets/admin/vendors/general/datatable/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            oTable = $('#listResults').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [[0, "DESC"]],
                "ajax": "{{route('admin.content.listing')}}",
                "columns": [
                    {"data": "id", searchable: false, sortable: false},
                    {"data": "title", searchable: false, sortable: false},
                    {"data": "action", searchable: false, sortable: false}
                ]
            });
        });
    </script>
@endsection

