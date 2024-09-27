<!DOCTYPE html>
<html>

<head>
    <title>{{ isset($user) ? 'Edit User' : 'Create User' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>{{ isset($user) ? 'Update User' : 'Create User' }}</h2>
        @if (!isset($user))
        <button class="btn btn-dark float-right" type="button" id="import-btn">Import</button>
        @endif

        <form id="userForm" action="{{ route('users.storeOrUpdate') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($user))
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            @endif
            <div class="m-3">
                <label for="name" class="form-label">Name</label>
                <input placeholder="Name" type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
            </div>
            <div class="m-3">
                <label for="email" class="form-label">Email</label>
                <input placeholder="Email" type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
            </div>
            <div class="m-3">
                <label for="image" class="form-label">Avatar</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            @if(isset($user->image))
            <div class="m-3">
                <img src="{{ asset('storage/'. $user->image) }}" alt="User Avatar" width="100" height="100">
            </div>
            @endif      
            <div class="m-3">
                <label for="password" class="form-label">Password</label>
                <input placeholder="Password" type="password" class="form-control" id="password" name="password" value="{{ old('password', $user->password ?? '') }}">
            </div>
            <a class="btn btn-secondary" href="{{ route('users.index') }}">Back</a>
            <button type="submit" class="btn btn-primary float-right">
                {{ isset($user) ? 'Update' : 'Create' }}
            </button>
        </form>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">User Import</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="file" class="form-label">Choose import file</label>
                    <input type="file" class="form-control" id="file" name="file" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirmImport">Import</button>
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

    $('#userForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                window.location.href = "{{ route('users.index') }}";
            },
            error: function(response) {
                alert('An error occurred.');
            }
        });
    });

    // Handle import 
    $('#import-btn').click(function() {
        $('#importModal').modal('show');
    });

    $('#confirmImport').on('click', function(event) {
        event.preventDefault();

        var file = $('#file')[0].files[0];
        var formData = new FormData();
        formData.append('file', file);

        $.ajax({
            type: 'POST',
            url: '/users/import',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#importModal').modal('hide');
                    alert(response.success);
                    window.location.href = "{{ route('users.index') }}";
                } else {
                    alert('Import failed. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while importing users. Please try again.');
            }
        });
    });
</script>