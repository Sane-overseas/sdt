@extends('layouts.app')
  
@section('content')
<style type="text/css">
   
    div#paidSchools_info {
        margin-left: 35px;
        padding: 0px;
    }  
</style>
<div class="container">
        <div class="row">
            <div class="col-lg-10 margin-tb">
                <h2 class="heading">Paid Schools</h2>
            </div>
        </div>
        <div class="card-body">
          	<table class="table table-bordered" id="paidSchools">
            <thead>
                <tr>
                    <th>Trainer</th>
                    <th>Verified By</th>
                    <th>School Name</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>Paid Date</th>
                    <th>School Complete Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paidSchools as $school)
                    <tr>
                        <td style="width:12%">
                            @foreach($trainers as $d_data)
                                @if($d_data['id'] == $school->user_id)
                                    <strong class="trainers">{{$d_data['instructor_name']}} - {{$d_data['instructor_code']}}</strong>
                                @endif
                            @endforeach
                        </td> 
                        <td>
                            @foreach($trainers as $d_data)
                                @if($d_data['id'] == $school->paid_by)
                                    <strong> {{$d_data['instructor_name']}}</strong>
                                @endif
                            @endforeach
                        </td>       
                        <td>
                         	@foreach($schools as $d_data)
                                @if($d_data['id'] == $school->school_id)
                                     <strong>{{$d_data['school_name']}}</strong>
                                @endif
                            @endforeach
                        </td> 
                        <td>
                         	@foreach($schools as $d_data)
                                @if($d_data['id'] == $school->school_id)
                                	@foreach($district as $dist)
	                                    @if($d_data['district_id'] == $dist['id'])
	                                    	{{$dist['district']}}
	                                    @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </td>
                        <td>{{$school->block}}</td>
                        <td>{{$school->paid_date}}</td>
                        <td>{{date('F Y', strtotime($school->end_date))}}</td>                       
                    </tr>
                @endforeach  
            </tbody>
            <tfoot>
                <tr>
                    <th>Trainer</th>
                    <th>Verified By</th>
                    <th>School Name</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>Paid Date</th>
                    <th>School Complete Date</th>
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
 const paidSchools =   $('#paidSchools').DataTable( {
    ordering: false,
    dom: "<'row'<'col-sm-1'B><'col-sm-5'i><'col-sm-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'><'col-sm-7'p>>",
    pageLength : 100,
    stateSave: true,
    buttons: [
       { 
          extend: 'excel',
          text: 'Download'
       }
    ]
});
$('#paidSchools tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input type="text" class="form-control" placeholder="'+title+'" />' );
});

$('tfoot').each(function () {
    $(this).insertAfter($(this).siblings('thead'));
});

// Apply the search
paidSchools.columns().every( function () {
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
