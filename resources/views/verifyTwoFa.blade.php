<!DOCTYPE html>
<html>
<head>
    <title>Two Factor Authentication Email</title>
</head>

<body>
  <h2>Hello {{$user['name']}}</h2>
  <br/>
  Your Two Factor Authentication Code is <b>{{$user["code"]}}</b>.
  Thanks
</body>
</html>
