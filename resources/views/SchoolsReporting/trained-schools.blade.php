 <!DOCTYPE html>
 <html>
 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title></title>
   
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
 <body>
<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Trained schools</h2>
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
        <table class="table table-bordered" id="trainedSchools">
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
                @foreach($schoolsWithAssigned as $school)
                    @if($school->status == 1)
                    <tr>
                        <td>{{$school->school_name}}</td>
                        <td>
                            @foreach($trainers as $d_data)
                                @if($d_data['id'] == $school->user_id)
                                     {{$d_data['instructor_name']}}
                                @endif
                            @endforeach
                        </td> 
                        <td>@foreach($district as $d_data)
                                @if($d_data['id'] == $school->district_id)
                                     {{$d_data['district']}}
                                @endif
                            @endforeach</td>
                        <td>{{$school->block}}</td>
                        <td>@foreach($schools as $d_data)
                                @if($d_data['id'] == $school->school_name)
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
 </body>
 </html>

<script type="text/javascript">

    $('#trainedSchools').DataTable( {
        ordering: false,
        info:     false,
        dom: 'Bfrtip',
        pageLength : 25,
        buttons: [
           'excel'
        ]
    });

</script>
