<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<div class="container mt-2">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2  class="heading">{{$trainer_data['instructor_name']}} - {{$trainer_data['instructor_code']}}</h2>
            </div>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="card-body">
        <table class="table table-bordered" id="imageTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{$trainer_data['instructor_name']}}'s Images</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>School Name</th>
                    <th>Intime & Outtime</th>
                    <th>Date</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trainer_data['images'] as $image)
                    <tr>
                        <td>{{$image['id']}}</td>
                        <td>{{$image['image']}}</td> 
                        <td>
                            @foreach($district as $d_data)
                                @if($d_data['id'] == $image['district'])
                                     {{$d_data['district']}}
                                @endif
                            @endforeach
                        </td>
                        <td>{{$image['bloack']}}</td>
                        <td>
                            @foreach($school as $d_data)
                                @if($d_data['id'] == $image['school_name'])
                                     {{$d_data['school_name']}}
                                @endif
                            @endforeach
                        </td>
                        <td style="width: 16%;">{{$image['intime']}} - {{$image['outtime']}}</td>
                        <td style="width: 10%;">{{date('d-m-Y', strtotime($image['created_at']))}}</td>
                        <td style="text-align: center; width: 5%;"><a href="{{ media_url('images', $image['image']) }}"><i class="fa fa-download save-icon" aria-hidden="true"></i></a></td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script type="text/javascript">

$('#imageTable').DataTable( {
    dom: 'Bfrtip',
    pageLength : 25,
    buttons: [
         'csv', 'excel', 'pdf', 'print'
    ]
} );

</script>