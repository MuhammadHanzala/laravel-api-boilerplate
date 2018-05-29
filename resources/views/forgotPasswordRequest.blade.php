<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>

<body>

  You're receiving this e-mail because you requested a password reset for your user account.
  Please go to the following page and choose a new password:
  <div>
    <a href="{{url('user/password-reset', $token)}}" target="_blank">{{url('user/password-reset', $token)}}</a>
  </div>
  <br />
  This Link will be expired in next 24 hours.
  <br />
  If you didn't request this change, you can disregard this email - we have not yet reset your password.
  <br />
  Thanks!
</body>
</html>
