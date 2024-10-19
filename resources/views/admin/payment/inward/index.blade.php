@extends('layouts.master')
@section('title') @lang('translation.Data_Tables') @endsection
@section('css')

<!-- DataTables -->
<link href="{{ URL::asset('/assets/admin/vendors/general/datatable/jquery.dataTables.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox/dist/jquery.fancybox.min.css" />
@endsection
@section('content')

@include('components.breadcum')

<div class="row">
    <div class="col-12">
        {!! success_error_view_generator() !!}
    </div>
    <div class="card">
        <div class="card-body">
            <div class="mb-2 text-right">
                <div class="wd-sl-modalbtn">
                    <a href="{{route('admin.payment.inward.create')}}">
                        <button type="button" class="btn btn-primary waves-effect waves-light"> Add
                        </button>
                    </a>
                </div>
            </div>
            <div class="table-responsive ">
                <table id="listResults" class="table dt-responsive mb-4  nowrap w-100 mb-">
                    <thead>
                    <tr>
                        <th>S.no</th>
                        <th>RefNo</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $i=1; @endphp
                                    @if(count($txn)>=1)
                                    @foreach($txn as $tx)
                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{ $tx->reference_no}}</td>
                                            <td class="text-left"><a href="{{ route('admin.account.show', $tx->party_id) }}">{{ $tx->accData->name }}</a></td>
                                            <td>{{ date('d-m-Y',strtotime($tx->txn_date))}}</td>
                                            <td>{{ $tx->txn_amount}}</td>
                                            <td>{{ $tx->reference_type}}</td>
                                            <td>
                                                <a href="{{ url('admin/print-receipt/'.$tx->id) }}" target="_blank" class="btn-sm btn-success">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                                <a href="{{ route('admin.payment.inward.edit', $tx->id) }}" class="btn-sm btn-info">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                               <button class="btn-sm btn-danger" type="button" onclick="deleteItem({{ $tx->id }})">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                                <form id="delete-form-{{ $tx->id }}" action="{{ url('admin/financial-delete/'.$tx->reference_type.'/'.$tx->id) }}" method="post"
                                                      style="display:none;">
                                                    @csrf
                                                </form>
                                            </td>
                                        </tr>
                                    @php $i++; @endphp
                                    @endforeach
                                    @else
                                    <tr>
                                      <td colspan="6" class="text-center">---No Data found for given criteria---</td>
                                    </tr>
                                    @endif
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<!-- Required datatable js -->
<script src="{{asset('/assets/admin/vendors/general/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('/assets/admin/vendors/general/datatable/jquery.dataTables.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox/dist/jquery.fancybox.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        oTable = $('#listResultseed').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [
                [0, "DESC"]
            ],
            "ajax": "{{route('admin.payment.inward.listing')}}",
            "columns": [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: true,
                    sortable: true
                },
                {
                    "data": "id",
                    searchable: false,
                    sortable: false
                },
                {
                    "data": "id",
                    searchable: true,
                    sortable: false
                },
                {
                    "data": "date",
                    searchable: true,
                    sortable: false
                },
                {
                    "data": "name",
                    searchable: true,
                    sortable: false
                },
                {
                    "data": "action",
                    searchable: false,
                    sortable: false
                }
            ]
        }).on( 'xhr.dt', function (e, settings, techNote, message) {
            console.log(message);
        });
    });
</script>
@endsection