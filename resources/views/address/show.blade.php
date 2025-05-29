<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Selected Address Details</h2>

    @if($selectedAddress)
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <td>{{ $selectedAddress->id }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $selectedAddress->address }}</td>
            </tr>
            <tr>
                <th>City</th>
                <td>{{ $selectedAddress->city }}</td>
            </tr>
            <tr>
                <th>State</th>
                <td>{{ $selectedAddress->state }}</td>
            </tr>
            <tr>
                <th>Pincode</th>
                <td>{{ $selectedAddress->pincode }}</td>
            </tr>
        </table>
    @else
        <p>No address found for the selected ID.</p>
    @endif
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
