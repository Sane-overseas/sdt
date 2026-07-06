@extends('layouts.app')
  
@section('content')

<div class="container">
        <div class="row">
            <div class="col-lg-10 margin-tb">
                <h2 class="heading">Districts</h2>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td><strong>Months</strong></td>
                        @foreach($totalSchools as $moths)
                            <th>{{$moths['month']}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Complete Schools</strong></td>
                        @foreach($totalSchools as $moths)
                            <td>{{$moths['count']}}</td>
                        @endforeach
                    </tr>  
                </tbody>
            </table>
            <table class="table table-bordered" id="districtsDetail">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>District</th>
                        <th>Total Students</th>
                        <th>Cerifide/Pending Students</th>
                        <th>Total Schools</th>
                        <th>Complete/Pending Schools</th>
                        <th>Required/Working Trainers</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($district as $data)
                        <tr>
                           <td>{{$data['id']}}</td>
                           <td><strong>{{$data['district']}}</strong></td>
                           <td>{{$data['total_students']}}</td>
                           <td><?php $total = 0; ?>
                                @foreach($distribution as $d_data)
                                    @if($d_data['district'] == $data['district'] && $d_data['complete_students'])
                                        <?php 
                                            $lineTotal = $d_data['complete_students'];
                                            $total+=$lineTotal;
                                        ?>                             
                                    @endif
                                @endforeach
                                {{$total}}/ {{$data['total_students']-$total}}
                           </td>
                           <td>{{$data['totol_schools']}}</td>
                           <td>
                              <?php $complete = 0; ?>
                               @foreach($schoolsWithAssigned as $school)
                                    @if($school->district_id == $data['id'] && $school->status == 1)
                                         <?php $complete++ ?>
                                    @endif
                               @endforeach
                               {{$complete}}/ {{$data['totol_schools'] - $complete}}
                           </td> 
                           <td>{{$data['trainer_required']}}/
                                <?php $working = 0; ?>
                                @foreach( $workingTrainers as $trainer)
                                    @if($trainer['district'] == $data['id'])
                                         <?php $working++ ?>
                                    @endif
                                @endforeach
                                {{$working}}
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
   let  districtsDetail = $('#districtsDetail').DataTable( {
        ordering: false,
        dom: 'Bfrtip',  
         pageLength : 25,
         buttons: [
            'excel'
        ]
    });

    districtsDetail.on('click', 'tbody tr', function (evt) {
        let data = districtsDetail.row(this).data();
        let id = $(this).find("td:first").text();
       
        let newUrl = "districts_data/"+id;
        location.href= newUrl;
  
    });
</script>
@endsection