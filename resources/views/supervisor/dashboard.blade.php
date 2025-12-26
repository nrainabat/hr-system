<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Dashboard | iManageHR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="bg-light">

<nav class="navbar" style="background:#A94064;">
    <div class="container">
        <span class="navbar-brand text-white fw-bold">iManageHR</span>

        <form method="POST" action="/logout">
            @csrf
            <button class="btn btn-light btn-sm">Logout</button>
        </form>
    </div>
</nav>

<div class="container py-5">
    <h2 class="fw-bold">Supervisor Dashboard</h2>
    <p class="text-muted">Welcome, {{ auth()->user()->name }}</p>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Assigned Interns</h5>
                <p class="fw-bold fs-4">—</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Attendance Review</h5>
                <p class="text-muted">Pending approvals</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
