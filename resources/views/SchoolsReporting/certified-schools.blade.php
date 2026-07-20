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
            <h2 class="heading ">Certified Schools</h2>
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
        <table class="table table-bordered" id="certifiedSchools">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Trainer</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>School Name</th>
                    <th>Total Students</th>
                </tr>
            </thead>
            <tbody>
                @foreach($distribution as $school)
                    <tr>
                        <td>@foreach($schools as $d_data)
                                @if($d_data['school_name'] == $school['school_name'] && $d_data['block'] == ($school['block'] ?? $school['bloack'] ?? null))
                                     {{$d_data['id']}}
                                @endif
                            @endforeach</td>
                        <td>
                            @foreach($trainers as $d_data)
                                @if($d_data['id'] == $school['user_id'])
                                     {{$d_data['instructor_name']}}
                                @endif
                            @endforeach
                        </td> 
                        <td>{{$school['district']}}</td>
                        <td>{{$school['block'] ?? $school['bloack'] ?? ''}}</td>
                        <td>{{$school['school_name']}}</td>
                        <td>{{$school['complete_students']}}</td>                      
                    </tr>
                @endforeach  
            </tbody>
        </table>
    </div>
</div>
 </body>
 </html>

<script type="text/javascript">

    $('#certifiedSchools').DataTable( {
        ordering: false,
         info:     false,
        dom: 'Bfrtip',
        pageLength : 25,
        buttons: [
            'excel'
        ],
    });

</script>
