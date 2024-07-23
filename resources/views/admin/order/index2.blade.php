<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Page</title>
    <!-- Bootstrap 3 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        /* Optional: Custom styling */
        .table-bordered th, .table-bordered td {
            border: 1px solid #ddd;
        }
        .filters {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="page-header">Order List</h1>

    <!-- Filters -->
    <div class="row filters">
        <div class="col-md-4">
            <input type="text" class="form-control" id="search" placeholder="Search orders...">
        </div>
        <div class="col-md-4">
            <select class="form-control" id="statusFilter">
                <option value="">All Statuses</option>
                <option value="Completed">Completed</option>
                <option value="Pending">Pending</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-md-4">
            <select class="form-control" id="recordsPerPage">
                <option value="5">5 records per page</option>
                <option value="10">10 records per page</option>
                <option value="15">15 records per page</option>
                <option value="20">20 records per page</option>
            </select>
        </div>
    </div>

    <!-- Order Table -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Total</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <!-- Example Order Rows -->
        <tr>
            <td>1</td>
            <td>1001</td>
            <td>John Doe</td>
            <td>2024-07-22</td>
            <td><span class="label label-success">Completed</span></td>
            <td>$99.99</td>
            <td>
                <a href="#" class="btn btn-primary btn-xs">View</a>
                <a href="#" class="btn btn-danger btn-xs">Delete</a>
            </td>
        </tr>
        <tr>
            <td>2</td>
            <td>1002</td>
            <td>Jane Smith</td>
            <td>2024-07-21</td>
            <td><span class="label label-warning">Pending</span></td>
            <td>$49.99</td>
            <td>
                <a href="#" class="btn btn-primary btn-xs">View</a>
                <a href="#" class="btn btn-danger btn-xs">Delete</a>
            </td>
        </tr>
        <!-- Repeat for other orders -->
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <li>
                <a href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">4</a></li>
            <li><a href="#">5</a></li>
            <li>
                <a href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<!-- Bootstrap 3 JS and dependencies -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    // JavaScript to handle filtering and pagination
    document.getElementById('search').addEventListener('input', function() {
        // Implement search functionality
        console.log('Search:', this.value);
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        // Implement filter by status functionality
        console.log('Filter by status:', this.value);
    });

    document.getElementById('recordsPerPage').addEventListener('change', function() {
        // Implement records per page functionality
        console.log('Records per page:', this.value);
    });
</script>

</body>
</html>
