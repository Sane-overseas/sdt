<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    </head>    
   <style>
        body {
            overflow-x: hidden;
        }
       .float-left,.float-right {
            font-size: 50px;
        }
       span.admin-dash {
            margin-left: 12px;
        }
        a.list-group-item.list-group-item-action.bg-light.active {
            background-color: #004857 !important;
        }
        
        .buttons {
            background-color: #0a0057 !important;
            color: #ffff !important;
        }
        #sidebar-wrapper .list-group {
            width: 160px !important;
            line-height: 8px !important;
        }
        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -15rem;
            -webkit-transition: margin .25s ease-out;
            -moz-transition: margin .25s ease-out;
            -o-transition: margin .25s ease-out;
            transition: margin .25s ease-out;
        }
        #sidebar-wrapper .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
        }
        #sidebar-wrapper .list-group {
            width: 15rem;
        }
        #page-content-wrapper {
            min-width: 100vw;
        }
        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }
        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }
            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }
            #wrapper.toggled #sidebar-wrapper {
                margin-left: -15rem;
            }
        }
        .admin-sidebar {
            padding: 0px;
            width: 167px !important;
        }

        .tab-content.admin-content {
            width: 86% !important;
        }
    </style>
    <body>
        <div class="row d-flex admin-page" id="wrapper">
        <!-- Sidebar -->
            <div class="bg-light border-right admin-sidebar" id="sidebar-wrapper">
                <div  id="myTab" class="list-group list-group-flush">
                    <a href="#dashboard" data-toggle="tab" class="list-group-item list-group-item-action bg-light active">
                    <i class="bi bi-speedometer2 dash-icons"></i><span class="admin-dash">Dashboard</span></a>
                    <a href="#workorder" data-toggle="tab" class="list-group-item list-group-item-action bg-light"><i class="bi bi-person-arms-up dash-icons"></i><span class="admin-dash">Trainers</span></a>
                    <a href="#log" data-toggle="tab" class="list-group-item list-group-item-action bg-light"><i class="bi bi-file-earmark-arrow-down-fill dash-icons"></i><span class="admin-dash">Logs</span></a>
                </div>
            </div>
            <!-- /#sidebar-wrapper -->
            <!-- Page Content -->
            <div class="tab-content admin-content ">
                <div id="dashboard" class="tab-pane active">
                    @include('admin.dashboard')
                </div>
                <div id="workorder" class="tab-pane fade">
                    @include('admin.trainer')
                </div>  
                <div id="log" class="tab-pane fade">
                    @include('admin.logs')
                </div>
            </div>     
            <!-- /#page-content-wrapper -->
        </div>  
        <!-- /#wrapper -->
    </body>
</html>
<script type="text/javascript">
    $('a[data-toggle="tab"]').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
        var id = $(e.target).attr("href");
        localStorage.setItem('activeTab', id)
    });

    var selectedTab = localStorage.getItem('activeTab');
    if (selectedTab != null) {
        $('a[data-toggle="tab"][href="' + selectedTab + '"]').tab('show');
    }
</script>