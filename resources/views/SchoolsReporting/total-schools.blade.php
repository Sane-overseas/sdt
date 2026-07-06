 <!DOCTYPE html>
 <html>
 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
     <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
 <body>
<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Total Schools</h2>
        </div>
        <div class="col-md-2">
         <a href="{{ route('dashboard') }}" class="float-right back-button " style="text-decoration:none">BACK</a>
        </div>
    </div> 
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="card-body">
        <table class="table table-bordered" id="trainer">
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
                                @else
                                    0    
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach($distribution as $data)
                                @if($school['id'] == $data['school_id'])
                                    <span class="pending">{{$school['total_students'] - $data['complete_students']}}</span>
                                @else
                                    {{$school['total_students']}} 
                                @endif                     
                            @endforeach
                        </td> 
                        <td>
                            @if($school['paid_status'] == 1)
                                <span class="compete">Paid</span>
                            @else
                                <span class="pending">Unpaid</span>
                            @endif
                        </td>                  
                    </tr>
                @endforeach  
            </tbody>
        </table>
    </div>
</div>
 </body>
 </html>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script type="text/javascript">

    $('#trainer').DataTable( {
        ordering: false,
        dom: 'Bfrtip',
        pageLength : 25,
        buttons: [
            'excel'
        ]
    });

</script>