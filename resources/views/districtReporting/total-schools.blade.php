<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Total Schools</h2>
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
        <table class="table table-bordered" id="totalDisScholls">
            <thead>
                <tr>
                    <th>#</th>
                    <th>School Code</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>School Name</th>
                    <th>Total Students</th>
                    <th>Complete Students</th>
                    <th>Pending Students</th>
                    <th>UC Status</th>
                    <th>Paid Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schools as $school)
                    <tr>
                        <td>{{$school['id']}}</td>
                        <td>{{$school['school_code']}}</td> 
                        <td>@foreach($district as $d_data)
                                @if($d_data['id'] == $school['district_id'])
                                     {{$d_data['district']}}
                                @endif
                            @endforeach</td>
                        <td>{{$school['block']}}</td>
                        <td>{{$school['school_name']}}</td>
                        <td>{{$school['total_students']}}</td> 
                        <td>
                            @foreach($distribution as $data)
                                @if($school['id'] == $data['school_id'])
                                    <span class="compete">{{$data['complete_students']}}</span>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach($distribution as $data)
                                @if($school['id'] == $data['school_id'])
                                    <span class="pending">{{$school['total_students'] - $data['complete_students']}}</span>
                                @endif                     
                            @endforeach
                        </td> 
                        <td>
                            @if($school['completion_status'] == 1)
                                <span class="compete">Received</span>
                            @else
                                <span class="pending">UC Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($school['paid_status'] == 1)
                                <span class="paid">Paid</span>
                            @else
                                <span class="unpaid">Pending</span>
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
   $('#totalDisScholls').DataTable( {
        ordering: false,
        dom: 'Bfrtip', 
         pageLength : 25, 
         buttons: [
            'excel'
        ]
    });
</script>
