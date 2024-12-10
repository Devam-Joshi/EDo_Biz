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
                    <a href="{{route('admin.inquiry-new.create')}}">
                        <button type="button" class="btn btn-primary waves-effect waves-light"> Add
                        </button>
                    </a>
                </div>
            </div>
            <div class="table-responsive ">
                <table id="listResults11" class="table dt-responsive mb-4  nowrap w-100 mb-">
                    <thead class="bg-primary text-white">
                            <tr>
                                <th>S.no</th>
                                <th>Supplier</th>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Remark</th>
                                <th>Actions</th>
                            </tr>
                    </thead>
                    <tbody>
                            @if(!empty($inquiry))
                                @foreach($inquiry as $key => $pur)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td class="text-left">{{ $pur->name }}
                                                <br><small>{{ $pur->state ? $pur->state->name:''}},{{$pur->city}}</small>
                                            </td>
                                            <td><a href="{{url('admin/print-sale-inquery/'.$pur->id)}}" title="Bill Print/ View" target="_blank">
                                                {{ $pur->invoice_No}}</a>
                                            </td>
											<td>{{ myDateFormat($pur->saleDate) }}</td>
                                            <td class="text-right">{{ number_format($pur->bill_amount,2)}}</td>
                                           <td><a href="{{url('admin/update-sale-inquery-status/'.$pur->billing_status)}}">Unbilled</a></td>
										   <td></td>
                                            <td class="action">
                                                <a href="{{ route('admin.inquiry-new.edit', $pur->id) }}" class="text-primary" target="_blank"><i class="fa fa-pen"></i></a> 
												 <a href="{{url('admin/newinquiry-to-inquiry/'.$pur->id)}}" class="text-secondary" target="_blank" title="Sale order to bill">
                                                    <i class="fa fa-list" aria-hidden="true"></i>
                                                </a>
                                                <a href="{{url('admin/print-sale-inquery/'.$pur->id)}}" class="text-success" title="Bill Print/ View" target="_blank" title="Print or view"><i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                                <a href="{{ route('admin.inquiry-new.show', $pur->id) }}" class="text-info" title="Edit order">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <span class="text-danger" type="button" onclick="deleteItem({{ $pur->id }})" title="Delete order">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </span>
                                                <form id="delete-form-{{ $pur->id }}" action="{{ route('admin.inquiry-new.destroy', $pur->id) }}" method="post"
                                                      style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                            </td>
                                        </tr>
                                    @endforeach
                        
                        @else
                           <tr>
                                <td colspan="7" class="text-center"> ---No Record Found---</td>
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
        oTable = $('#listResults').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [
                [0, "DESC"]
            ],
            "ajax": "{{route('admin.category.listing')}}",
            "columns": [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: true,
                    sortable: true
                },
                {
                    "data": "name",
                    searchable: true,
                    sortable: false
                },
                {
                    "data": "description",
                    searchable: true,
                    sortable: false
                },
                {
                    "data": "action",
                    searchable: false,
                    sortable: false
                }
            ]
        });
    });
</script>
@endsection