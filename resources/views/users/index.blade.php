<!DOCTYPE html>
<html>

<head>
    <title>Users</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet"> -->
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center"><b>User Details</b></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label for="user_id" class="form-label">Choose a user:</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Select User</option>
                    @foreach ($users as $user)
                    <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary m-3 float-right" type="button" id="filter-btn">Filter</button>
            </div>
            <div class="col-md-8">
                <a href="{{ route('users.form') }}" class="btn btn-primary btn-sm m-1 float-right">Create User</a>
                <a href="{{ route('users.export') }}" class="btn btn-dark btn-sm m-1 float-right">Export</a>

                <table class="table table-bordered" id="users-table">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Avatar</th>
                            <th class="text-center">Created At</th>
                            <th class="text-center" width="100px">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(function() {
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('users.data') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'image',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        // Handle filter button click
        $('#filter-btn').click(function() {
            var userId = $('#user_id').val();
            table.ajax.url("{{ route('users.data') }}" + "?user_id=" + userId).load();
        });

        // Handle delete 
        var userId;
        $('body').on('click', '.deleteUser', function() {
            userId = $(this).data('id');
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').click(function() {
            $.ajax({
                type: "DELETE",
                url: "/users/delete/" + userId,
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    table.ajax.reload();
                    // alert(response.success);
                }
            });
        });

    });
</script>