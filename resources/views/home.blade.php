<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>iManageHR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .left-panel {
            background-color: #123456;
            color: white;
        }
        .form-control:focus {
            border-color: #123456;
            box-shadow: 0 0 0 0.25rem rgba(18, 52, 86, 0.25);
        }
    </style>
</head>

<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            
            {{-- Main Card Container --}}
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 20px;">
                <div class="row g-0">
                    
                    {{-- LEFT PART: System Name & Branding (Made Smaller) --}}
                    {{-- Changed col-md-6 to col-md-5 to reduce width --}}
                    <div class="col-md-5 left-panel d-flex flex-column justify-content-center align-items-center p-4 text-center">
                        <div class="mb-3">
                            {{-- Changed display-1 to display-3 for smaller icon --}}
                            <i class="bi bi-people-fill display-3"></i>
                        </div>
                        {{-- Changed display-5 to display-6 for smaller text --}}
                        <h1 class="fw-bold display-6 mb-2">iManageHR</h1>
                        <p class="small opacity-75">
                            Simplify Employee Management,<br>Attendance & Performance.
                        </p>
                        <hr class="w-25 border-top border-2 opacity-100 my-3">
                        <small class="opacity-75" style="font-size: 0.75rem;">© {{ date('Y') }} System</small>
                    </div>

                    {{-- RIGHT PART: Login Section (Made Wider) --}}
                    {{-- Changed col-md-6 to col-md-7 to fill remaining space --}}
                    <div class="col-md-7 bg-white p-5 d-flex flex-column justify-content-center">
                        <div class="px-md-3">
                            <h3 class="fw-bold mb-2" style="color: #123456;">Welcome Back</h3>
                            <p class="text-muted mb-4">Please login to your account.</p>

                            @if(session('error'))
                                <div class="alert alert-danger py-2 fs-6">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login.submit') }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                        <input type="text" name="username" class="form-control border-start-0 bg-light ps-1" placeholder="Enter username" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-muted">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                        <input type="password" name="password" class="form-control border-start-0 bg-light ps-1" placeholder="Enter password" required>
                                    </div>
                                </div>

                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn text-white py-2 fw-bold shadow-sm" style="background-color: #123456; border-radius: 10px;">
                                        LOGIN
                                    </button>
                                </div>

                                <div class="text-center">
                                    <small class="text-muted">Having trouble? Contact Administrator.</small>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            {{-- End Main Card --}}

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>