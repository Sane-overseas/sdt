@extends('layouts.app')
  
@section('content')

<div class="container">
        <div class="row">
            <div class="col-lg-10 margin-tb">
                <h2 class="heading">Unpaid Schools</h2>
            </div>
        </div>
        <div class="card-body">
          	<table class="table table-bordered" id="paidSchools">
            <thead>
                <tr>
                    <th>Trainer</th>
                    <th>School Name</th>
                    <th>District</th>
                    <th>Schools Complete</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unpaid_schools as $school)
                    <tr>
                        <td> <strong>{{$school->instructor_name}}</strong></td>        
                        <td>
                         	@foreach($schools as $d_data)
                                @if($d_data['id'] == $school->school_name)
                                     <strong>{{$d_data['school_name']}}</strong>
                                @endif
                            @endforeach
                        </td> 
                        <td>
                        	@foreach($district as $dist)
                                @if($school->district == $dist['id'])
                                	{{$dist['district']}}
                                @endif
                            @endforeach    
                        </td>
                        <td>{{$school->end_date}}</td>                    
                    </tr>
                @endforeach  
            </tbody>
        </table> 
    	</div>  
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  $('#paidSchools').DataTable( {
        ordering: false,
        dom: 'Bfrtip',  
         pageLength : 25,
         buttons: [
            'excel'
        ]
    });

</script>
@endsection
