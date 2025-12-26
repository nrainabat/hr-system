<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | iManageHR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #873260;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px;
        }
        .sidebar a:hover {
            background: #957A81;
        }
    </style>
</head>

<body>
<div class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h4 class="text-white fw-bold mb-4">iManageHR</h4>
        <a href="#">Dashboard</a>
        <a href="{{ route('admin.users.create') }}">
        + New User
    </a>
        <a href="#">Manage Users</a>
        <a href="#">Intern Records</a>
        <a href="#">Reports</a>

        <form method="POST" action="/logout" class="mt-4">
            @csrf
            <button class="btn btn-light w-100">Logout</button>
        </form>
    </div>

    <!-- Content -->
    <div class="flex-fill p-4">
        <h2 class="fw-bold">Admin Dashboard</h2>
        <p class="text-muted">Welcome, {{ auth()->user()->name }}</p>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h5>Total Users</h5>
                    <p class="fs-3 fw-bold">—</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h5>Total Interns</h5>
                    <p class="fs-3 fw-bold">—</p>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>