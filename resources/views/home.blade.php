<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iManageHR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background: #f8f9fa; 
        }
        .login-box { 
            max-width: 400px; 
            margin: 120px auto; 
        }
    </style>
</head>

<body>



<div class="login-box">
    <div class="card shadow">
        <div class="card-body p-4">

            <h4 class="fw-bold text-center mb-4">User Login</h4>

            @if(session('error'))
                <div class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="d-grid mt-4">
                    <button class="btn text-white" style="background:#123456;">
                        Login
                    </button>
                </div>
            </form>

            <p class="text-center text-muted mt-3" style="font-size:14px;">
                Please contact administrator if you face login issues.
            </p>

        </div>
    </div>
</div>

</body>
</html>
