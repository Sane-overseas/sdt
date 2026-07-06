<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">OnGoing Schools</h2>
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
        <table class="table table-bordered" id="ongoingdd">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Trainer</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>School Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ongoing_schools as $school)
                    @if($school['status'] == 0)
                        <tr>
                            <td>{{$school['school_name']}}</td>
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
                            <td>
                                @foreach($schools as $d_data)
                                    @if($d_data['id'] == $school['school_name'])
                                         {{$d_data['school_name']}}
                                    @endif
                                @endforeach
                            </td>                      
                        </tr>
                    @endif    
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
   $('#ongoingdd').DataTable( {
        ordering: false,
        dom: 'Bfrtip', 
         pageLength : 25, 
         buttons: [
            'excel'
        ]
    });
</script>
