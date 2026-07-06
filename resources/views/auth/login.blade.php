<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<style type="text/css">
.gradient-custom {
/* fallback for old browsers */
background: #6a11cb;

/* Chrome 10-25, Safari 5.1-6 */
background: -webkit-linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));

/* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1))
}
ul.text-sm.text-red-600.space-y-1.mt-2 {
    color: red;
}
.login-btn {
    border: 2px solid black !important;
}
.login-input {
    border: 2px solid #00000052 !important;
}
.login-btn {
    border-radius: 8px;
    padding: 5px;
    color: #fff;
    background-color: #3460f2;
    border: 2px solid #355df0 !important;
}
img.hp-logo {
    float: inline-end;
    width: 37% !important;
}
img.site-logo {
    width: 105%;
}

@media only screen and (max-width: 600px) {
    img{
       width: 100% !important;
    }
    .p-login {
        font-size: 13px;
    }
    ul.text-sm.text-red-600.space-y-1.mt-2 {
        font-size: 13px;
    }
}   
</style>
<body>
<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card login-div text-dark">
          <div class="card-body p-4">
            <form method="POST" action="{{ route('login') }}">
                 @csrf
                 <div class="row">
                    <div class="login-image col">
                      <img class="site-logo" src="{{ asset('/images/logo1.png') }}" alt="" title="" >
                     </div>
                    <div class="col">
                          <img class="hp-logo"  src="{{ asset('/images/hp-logo.png') }}" alt="" title="" >
                     </div>
                 </div>
                <div class="md-4">
                  <p class="text-dark-50 mb-2 text-center p-login">Please enter your Email and Password!</p>
                  <div class="form-outline form-white mb-4">
                    <label class="form-label" for="typeEmailX">Email</label>
                    <input type="email" id="email" name="email" class="form-control form-control login-input" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                  </div>
                  <div class="form-outline form-white mb-2">
                     <label class="form-label" for="typePasswordX">Password</label>
                    <input type="password" id="password" name="password" class="form-control form-control login-input" />
                     <x-input-error :messages="$errors->get('password')" class="mt-2" />
                  </div>
                    <div class="form-outline form-white mb-4">
                    <input type="checkbox" onclick="myFunction()"> Show Password&nbsp; &nbsp;   
                     <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </div>
                  <div class="text-center btn-div">
                  <button class=" px-5 login-btn" type="submit">Login</button>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</html>
<script>
function myFunction() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>