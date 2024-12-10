@extends('layouts.master')
@section('title') @lang('translation.Data_Tables') @endsection
@section('css')

<!-- DataTables -->
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.3/fh-4.0.1/r-3.0.3/datatables.min.css" rel="stylesheet">
 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox/dist/jquery.fancybox.min.css" />
@endsection
@section('content')

@include('components.breadcum')

<div class="row">
    <div class="col-12">
        {!! success_error_view_generator() !!}

    </div>
    <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Accounts List 
                                    <a href="{{route('admin.account.create')}}" class="btn btn-outline-info btn-sm float-end rounded-pill ms-1">
                                        <i class="fa fa-plus" aria-hidden="true"></i>Add New
                                    </a>
                                    <a href="{{ route('admin.client-product-association') }}" class="btn btn-outline-danger btn-sm float-end rounded-pill ms-1">
                                        <i class="fa fa-plus" aria-hidden="true"></i>Ac related Product
                                    </a>
                                    <a href="{{ route('admin.partywise-overdue-bills') }}"><span class="float-end btn btn-outline-warning btn-sm ms-1 me-1 rounded-pill">Overdue Alerts</span></a>
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                            <div class="row border shadow mb-2 p-2 bg-light">
                              <div class="col-md-2">
                                <div class="form-group">
                                  <label> Account Group</label>
                                <select name="acGroup" id="acGroup" class="form-control">
                                  <option value="">-Select Group-</option>
                                    @if(isset($acgroup))
                                        @foreach($acgroup as $category)
                                            <optgroup label="{{ $category->name }}">
                                                    @foreach($category->child as $child)
                                                        <option value="{{ $child->id }}">&#8594;{{ $child->name }}</option>

                                                        @foreach($child->child as $cat)
                                                            <option value="{{ $cat->id }}">&nbsp;&nbsp;&nbsp;&#8226;&nbsp;{{ $cat->name }}</option>
                                                        @endforeach
                                                    @endforeach
                                            </optgroup>
                                        @endforeach
                                    @endif
                                </select>
                              </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <label> State</label>
                                    <select name="acstate" id="acstate" class="form-control">
                                        <option value="">-Select State-</option>
                                        @if(isset($state))
                                            @foreach($state as $st)
                                            <option value="{{$st->id}}">{{$st->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                              </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <label> City </label>
                                <select name="accity" id="accity" class="form-control">
                                  <option value="">-Select city-</option>
                                    @if(isset($city))
                                        @foreach($city as $ct)
                                        <option value="{{$ct->id}}">{{$ct->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                              </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="acType" id="acType" class="form-control">
                                        <option value="" >Select Type</option>
                                        <option value="1">Distributor</option>
                                        <option value="2">Whole Seller</option>
                                        <option value="3">other</option>
                                    </select>
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                    <label>Price Group</label>
                                    <select name="priceGroup" id="priceGroup" class="form-control">
                                        <option value="">--Price Group--</option>
                                        <option value="1">Retail Price</option>
                                        <option value="2">WS Price</option>
                                    </select>
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="acstatus" id="acstatus" class="form-control">
                                        <option value="">--Status--</option>
                                        <option value="1">Active</option>
                                        <option value="0">In-Active</option>
                                        <option value="2">Blocked</option>
                                    </select>
                                </div>
                              </div>
                              <center style="margin:auto">
                                <span class="btn btn-sm btn-danger rounded-pill mt-2" id="filterBtn"><i class="fa fa-search"> Search </i></span>
                                </center>
                            </div>
                            <div class="table-responsive ">
                            <table id="users_datatables" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th style="width:150px">Name</th>
                                   <!-- <th>Image</th> -->
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Group</th>
								    <th>Type</th>
                                    <th>Block Status</th>
                                    <th>Opn. Bal</th>
                                    <th>Dis.%</th>
                                    <th>By User</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                            </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
</div>

@endsection

@section('script')
<!-- Required datatable js -->
<script src="{{asset('/assets/admin/vendors/general/validate/jquery.validate.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox/dist/jquery.fancybox.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.3/fh-4.0.1/r-3.0.3/datatables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        oTable = $('#listResults').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [
                [0, "DESC"]
            ],
            "ajax": "{{route('admin.branch.listing')}}",
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
                    "data": "address",
                    searchable: true,
                    sortable: false
                },
                {
                    "data": "status",
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

    $(document).ready(function(){
    var table = $('#users_datatables').DataTable({
    processing: true,
    serverSide: true,
    order: [[0, "desc" ]],
    "lengthChange": true,
    "pageLength": 50,
    dom: 'Blfrtip',
   buttons: [
    'copy', 'csv', 'excel', 'pdf',
        {
            extend: 'copy',
            extend:'excelHtml5',
            extend: 'print',
            exportOptions: {
                columns: ':visible'
            }
        },
        'colvis'
    ],
    columnDefs: [ {
        targets: 0,
        visible: false
    } ],
    
    "aLengthMenu": [[10, 25, 50, 100, 200,9999], [10, 25, 50, 100, 200, "All"]],

    "ajax":{
    "url": '{!! route('admin.account.listing') !!}',
    "dataType": "json",
    "type": "POST",
    "error": function (xhr, error, code) {
            console.log(xhr, code);
        },
    "data":function(data){

      var acgroup=$('#acGroup').find(":selected").val();
      var actype=$('#acType').find(":selected").val();
      var acpricegroup=$('#priceGroup').find(":selected").val();
      var acstatus=$('#acstatus').find(":selected").val();
      var accity=$('#accity').find(":selected").val();
      var acstate=$('#acstate').find(":selected").val();
   
          // Append to data
          data.acgroup = acgroup;
          data.actype = actype;
          data.acpricegroup = acpricegroup;
          data.acstatus = acstatus;
          data.accity = accity;
          data.acstate = acstate;
          
          data._token= "{{csrf_token()}}";
       }

    },


    columns: [
      { data: 'id', name: 'id', orderable:true },
      { data: 'name', name: 'name', orderable:true,width:'150px',  },
     // { data: 'banner', name: 'banner_image', orderable:false  },
      { data: 'phone', name: 'phone', orderable:false},
      { data: 'city', name: 'city', orderable:true},
      { data: 'state', name: 'state', orderable:true},
      { data: 'acgroup', name: 'group', orderable:true},
      { data: 'type', name: 'type', orderable:true},
      { data: 'block_status', name: 'block_status', orderable:false},
      { data: 'opening', name: 'openingBalance', orderable:false},
      { data: 'disrate', name: 'discount_rate', orderable:true },
      { data: 'user_name', name: 'user_id', orderable:true },
      { data: 'action', name: 'action', orderable:false }
    ],
    "columnDefs": [
    { "searchable": false, "targets": 0 }
    ]
    ,language: {
        searchPlaceholder: "Search by id,name or slug"
    }
    });

    $('#filterBtn').click(function(){
      table.draw();
   });

  });
</script>
@endsection