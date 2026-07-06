<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Assigned Schools</h2>
        </div>
        <div class="col-md-2">
         <a href=" {{ url()->previous() }}" class="float-right back-button " style="text-decoration:none">BACK</a>
        </div>
    </div> 
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="card-body">
        <table class="table table-bordered" id="assignedDisScholls">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Trainer</th>
                    <th>Trainer Code</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>School Name</th>
                    <th>UC Status</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asigned_schools as $school)
                    <tr>
                        <td>{{$school['school_name']}}</td>
                        <td>
                            @foreach($trainers as $d_data)
                                @if($d_data['id'] == $school['user_id'])
                                     {{$d_data['instructor_name']}}
                                @endif
                            @endforeach
                        </td> 
                        <td>
                            @foreach($trainers as $d_data)
                                @if($d_data['id'] == $school['user_id'])
                                     {{$d_data['instructor_code']}}
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
                        <td style="width: 14%;">
                            @if($school['uc_submitted'] == 1)
                                <span class="compete">Received</span>
                            @else
                                <span class="pending">UC Pending</span>
                            @endif
                        </td> 
                        <td>
                            @if($school['route_date'] != null && $school['status'] == 0)
                                <span class="pending">OnGoing</span>
                            @elseif($school['status'] == 1 && $school['paid_status'] == 0) 
                                <span class="compete">Completed</span>  
                            @elseif($school['route_date'] == null) 
                                <span class="not-started">Not Started</span> 
                            @elseif($school['paid_status'] == 1)  
                                 <span class="paid">Paid</span>    
                            @endif
                        </td>                     
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
   $('#assignedDisScholls').DataTable( {
        ordering: false,
        dom: 'Bfrtip', 
         pageLength : 25, 
         buttons: [
            'excel'
        ]
    });
</script>
