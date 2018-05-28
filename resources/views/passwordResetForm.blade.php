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
            }
            .email .col-md-6{
              display: inline-block;
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
                          <form class="form-horizontal" method="POST" action="{{ route('updatePassword') }}">
                                          {{csrf_field()}}
                                          <input type="hidden" name="token" value="{{ session('token') }}">

                                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                              <label for="email" class="col-md-4 control-label">E-Mail Address:
                                                <h4 class="email"> {{ session('email') }} </h4></label>
                                          </div>
                                          <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                              <label for="password" class="col-md-4 control-label">Password</label>

                                              <div class="col-md-6d">
                                                  <input id="password" type="password" class="form-control" min="6" name="password" required>

                                                  @if ($errors->has('password'))
                                                      <span class="help-block">
                                                          <strong>{{ $errors->first('password') }}</strong>
                                                      </span>
                                                  @endif
                                              </div>
                                          </div>

                                          <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                              <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                                              <div class="col-md-6">
                                                  <input id="password-confirm" type="password" class="form-control" min="6" name="password_confirmation" required>

                                                  @if ($errors->has('password_confirmation'))
                                                      <span class="help-block">
                                                          <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                      </span>
                                                  @endif
                                              </div>
                                          </div>

                                          <div class="form-group">
                                              <div class="col-md-6 col-md-offset-4">
                                                  <button type="submit" class="btn btn-primary">
                                                      Reset Password
                                                  </button>
                                              </div>
                                          </div>
                                      </form>
                                  </div>
                  </div>
          </div>

    </body>
</html>
