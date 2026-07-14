<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
</head>
<style type="text/css">
   .auth-btn {
        border-radius: 5px;
        padding: 5px 25px;
        border: 2px solid #fff;
    }
    a {
        text-decoration: none !important;
    }
    @media only screen and (max-width: 600px) {
       .auth-btn {
            font-size: 14px;
            border-radius: 5px;
            padding: 0px 15px;
        }
        .dropdown {
            position: absolute !important;
        }
        .d-image {
            position: relative !important;
        }
    }
</style>
<body>
<!-- Navbar-->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid justify-content-between">
    <!-- Left elements -->
    <div class="d-flex d-image">
      <!-- Brand -->
       <a href="{{ route('dashboard') }}">
           <img class="logo"  src="{{ asset('/images/logo1.png') }}" alt="" title="" style="width:60%">
        </a>
      </a>
    </div>
    <div>
       <img class="hp-logo"  src="{{ asset('/images/hp-logo.png') }}" alt="" title="" >
    </div>
    <!-- Right elements -->
  </div>
</nav>
<!-- Navbar -->
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>

