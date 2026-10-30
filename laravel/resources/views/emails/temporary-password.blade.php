<!DOCTYPE html>
<html>
<head>
    <title>Welcome to the Gym System</title>
</head>
<body>
    <h2>Hello, {{ $user->first_name }}!</h2>
    <p>An account has been created for you in the Gym Management System.</p>
    <p>Your temporary login credentials are:</p>
    <ul>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Temporary Password:</strong> <code>{{ $tempPassword }}</code></li>
    </ul>
    <p>For security reasons, you will be required to change this password immediately upon your first login.</p>
    <p>Best regards,<br>Gym Management Team</p>
</body>
</html>