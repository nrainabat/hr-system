<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome | iManageHR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body, * { font-family: 'Poppins', sans-serif; }
        .btn-custom { 
            background-color: #873260; 
            color: white; 
            border: none;
            transition: all 0.3s ease;
        }
        .btn-custom:hover { 
            background-color: #957A81; 
            color: white; 
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-light d-flex flex-column min-vh-100" style="padding-top: 70px;">

    <nav class="navbar navbar-expand-lg shadow-sm fixed-top" style="background-color: #873260;">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="{{ url('/') }}">iManageHR</a>
            
            </div>
    </nav>

    <section class="py-5">
        <div class="container text-center">
            <h1 class="fw-bold mb-3">Welcome to iManageHR System</h1>
            <p class="text-muted fs-5 mb-5">
                A smart platform designed to simplify HR processes such as
                intern management, attendance tracking and performance evaluation.
            </p>

            <a href="{{ route('login') }}" class="btn btn-custom px-5 py-3 shadow rounded-pill fs-5 fw-bold">
                Login
            </a>
            
        </div>
    </section>

    <section class="py-5 bg-white flex-grow-1">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="p-4 shadow-sm rounded bg-light h-100">
                        <h5 class="fw-bold" style="color: #873260;">Intern Management</h5>
                        <p class="text-muted">Add, track and update intern information with ease.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 shadow-sm rounded bg-light h-100">
                        <h5 class="fw-bold" style="color: #873260;">Attendance Monitoring</h5>
                        <p class="text-muted">Efficiently record and analyze intern attendance.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 shadow-sm rounded bg-light h-100">
                        <h5 class="fw-bold" style="color: #873260;">Performance Evaluation</h5>
                        <p class="text-muted">Conduct evaluations and track intern performance.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center py-4 text-white mt-auto" style="background-color: #873260;">
        <small>©{{ date('Y') }} iManageHR System. All Rights Reserved.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>