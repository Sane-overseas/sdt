@extends('layouts.app')
@section('title', 'Route Plans Schools')  
@section('content')
<style type="text/css">
    .dataTables_wrapper .dataTables_info {
        clear:none;
        margin-left:10px;
        padding-top:0;
    }
</style>
<div class="container">
        <div class="row">
            <div class="col-lg-10 margin-tb">
                <h2 class="heading">Route Plans Schools</h2>
            </div>
        </div>
        <form id="custom_form" action="{{ route('route-plan-custom-date') }}" method="get">
         <div style="margin: 20px 0px;" class="row">
            <strong>Date Filter:</strong>
            <input type="date" name="custom_date" class="form-control col-4"  />
            <button type="submit" class="btn btn-success filter ml-4 col-2">Submit</button>
            <a href="{{ route('route-plan-schools') }}" class=" ml-4 col-2 close-btn">Clear</a>  
        </div>
        </form> 
        <div class="card-body">
            <table class="table table-bordered" id="routeplanSchools">
            <thead>
                <tr>
                    <th>Trainer</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>School Name</th>
                    <th>Route Plan</th>
                    <th>Added by</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                 @foreach($addRoutePlan as $school)
                    <tr>
                        <td>
                            @foreach($trainers as $d_data)
                                @if($d_data['id'] == $school['user_id'])
                                     {{$d_data['instructor_name']}}
                                @endif
                            @endforeach
                        </td> 
                        <td>@foreach($district as $d_data)
                                @if($d_data['id'] == $school['district'])
                                     {{$d_data['district']}}
                                @endif
                            @endforeach</td>
                        <td>{{$school['block']}}</td>
                        <td>@foreach($schools as $d_data)
                                @if($d_data['id'] == $school['school_name'])
                                     {{$d_data['school_name']}}
                                @endif
                            @endforeach
                        </td> 
                        <td><i class="bi bi-calendar-date-fill"></i> {{$school['route_date']}}  <i class="bi bi-clock-fill"></i>  {{date('H:i', strtotime($school['start_route_plan']))}} - {{date('H:i', strtotime($school['end_route_plan']))}}</td>
                        <td>
                            @foreach($trainers as $d_data)
                                @if($d_data['id'] == $school['added_by_route_plan'])
                                     {{$d_data['instructor_name']}}
                                @endif
                            @endforeach
                        </td> 
                        <td><i class="bi bi-clock-fill"></i> {{date('d-m-Y : g:i A', strtotime($school['add_route_plan_date']))}}</td>                      
                    </tr>
                @endforeach   
            </tbody>
            <tfoot>
                <tr>
                    <th>Trainer</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>School Name</th>
                    <th>Route Plan</th>
                    <th>Added by</th>
                    <th>Created At</th>
                </tr>
            </tfoot>
        </table> 
        </div>  
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript">

 const routeplanSchools =   $('#routeplanSchools').DataTable( {
        ordering: false,
        dom: 'lifrtp',
        pageLength : 30,
        stateSave: true,
        searchHighlight: true,
        buttons: ['excel']
    });
$('#routeplanSchools tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input type="text" class="form-control" placeholder="'+title+'" />' );
});

$('tfoot').each(function () {
    $(this).insertAfter($(this).siblings('thead'));
});

// Apply the search
routeplanSchools.columns().every( function () {
    var that = this;

    $( 'input', this.footer() ).on( 'keyup change', function () {
        if ( that.search() !== this.value ) {
            that
                .search( this.value )
                .draw();
        }
    } );
});

</script>
@endsection