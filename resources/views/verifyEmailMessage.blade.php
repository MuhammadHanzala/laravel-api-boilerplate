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
                font-family: 'Raleway', sans-serif;
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
              justify-content: center;
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
            }

            .alert {
                font-size: 50px;
            }
            .alert-success{
              color: green;
            }
            .alert-warning{
              color: red;
            }

            .flex-container{
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: space-between;
            }

            .m-b-md {
                margin-bottom: 60px;
            }
        </style>
    </head>
    <body>
         <div class="flex-center content position-ref full-height">
                <div class="title m-b-md">
                  Schedule App
                </div>
                  @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                    @endif
                    @if (session('credential'))
                        <div class="alert alert-warning">
                          {{ session('credential') }}
                        </div>
                    @endif
          </div>

    </body>
</html>
