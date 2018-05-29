<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
              align-items: center;
              display: flex;
              flex-direction: column;
              justify-content: flex-start;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title{
              font-size: 84px;
              font-family: 'Raleway', sans-serif;
            }

            .alert {
                font-size: 36px;
            }
            .alert-success{
              color: green;
            }
            .alert-warning{
              color: red;
            }

            .container{
              flex: 1;
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: flex-start;
            }

            .m-b-md {
              margin: 30px;
            }
            .form-control{
                display: block;
                box-sizing: border-box;
                border-radius: 4px;
                border: 3px solid #ccc;
                -webkit-transition: 0.5s;
                transition: 0.5s;
                outline: none;
                margin: 10px 4px;
                padding: 10px 20px;
                font-size: 16px;
            }
            .form-control:focus {
                border:  3px solid rgb(12, 110, 146);
                }
            .email .col-md-6{
              display: inline-block;
            }
            .btn {
                background-color: rgba(255, 255, 255, 0); 
                border: 3px solid #035856;
                border-radius: 4px;
                padding: 10px 20px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 20px 4px;
                -webkit-transition-duration: 0.4s; /* Safari */
                transition-duration: 0.4s;
                cursor: pointer;
                color: #035856;
            }
            .btn:hover {
                background-color: #035856;
                color: white;
            }
        </style>
    </head>
    <body>
         <div class="flex-center content position-ref full-height">
                <div class="title m-b-md">
                  Schedule App
                </div>
                <div class="container">
                    <div class="panel-heading alert m-b-md">Reset Password</div>
                      <div class="panel-body">
                          <form class="form-horizontal" name="resetForm" method="POST" action="{{ route('updatePassword') }}">
                                          {{csrf_field()}}
                                          <input type="hidden" name="token" value="{{ session('token') }}">
                                            <p>Link will be expired within {{round(session('expiry'))}} hours</p>
                                            
                                          <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                                              <div class="container">
                                                  <input id="password" type="password" placeholder="New Password" class="form-control" min="6" name="password" required>

                                                  @if ($errors->has('password'))
                                                      <div class="help-block">
                                                          <strong>{{ $errors->first('password') }}</strong>
                                                      </div>
                                                  @endif
                                              </div>
                                          </div>

                                          <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                              <div class="container">
                                                  <input id="password-confirm" type="password" placeholder="Confirm Password" class="form-control" min="6" name="password_confirmation" required>

                                                  @if ($errors->has('password_confirmation'))
                                                      <div class="help-block">
                                                          <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                      </div>
                                                  @endif
                                              </div>
                                          </div>

                                          <div class="form-group">
                                              <div class="col-md-6 col-md-offset-4">
                                                  <input class="btn btn-primary" id="submitBtn" type="submit" value="Submit" onclick="this.disabled=true;this.value='Submitting, please wait...'; resetForm.submit()" />
                                              </div>
                                          </div>
                                      </form>
                                  </div>
                  </div>
          </div>

    </body>
</html>
