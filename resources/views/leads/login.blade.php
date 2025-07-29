<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads Portal Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .forgot-password {
            margin-top: 10px;
            font-size: 14px;
            color: #007bff;
            cursor: pointer;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }
        button{
            background-color: #e82b44;
            border: 2px solid #e82b44;
            font-weight: 600;
        }
        button:hover{
            color:  #e82b44;
            background-color:rgb(255, 255, 255);
            border: 2px solid #e82b44;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Madtrek Portal Login</h2>

    @if (isset($errors) && $errors->has('login'))
        <p class="error-message">{{ $errors->first('login') }}</p>
    @endif

    <form method="POST" action="{{ route('leads.login.submit') }}">
        @csrf
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
