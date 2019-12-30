@extends('layouts.template')

@section('title', 'Users')

@section('main')
    <h1>Users</h1>
    <form method="get" action="/admin/users2" id="searchForm">
        <div class="row">
            <div class="col-sm-7 mb-2">
                Filter Name Or Email
                <input type="text" class="form-control @error('search') is-invalid @enderror" name="search" id="search"
                       value="{{ request()->search }}" placeholder="Filter Name Or Email">
                @error('search')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-sm-5 mb-2">
                Sort by
                <select class="form-control" name="dropdown" id="dropdown">
                    <?php
                    $collections = [
                        "Name (A &rarr; Z)" => "name",
                        "Name (Z &rarr; A)" => "1",
                        "Email (A &rarr; Z" => "email",
                        "Email (Z &rarr; A)" => "2",
                        "Not active users" => "3",
                        "Admin users"=> "4"
                    ];

                    foreach ($collections as $collection  => $x_value) {

                        if ($x_value == $dropdown){
                            echo '<option value=' . $x_value . ' selected >' . $collection . '</option>';
                        }else{
                            echo '<option value=' . $x_value . '>' . $collection . '</option>';
                        }
                    }
                    ?>
                </select>

            </div>
        </div>
    </form>
    @include('shared.alert')
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Active</th>
                <th>Admin</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    @if($user->active == 1)
                        <td>&#10004;</td>
                    @else
                        <td></td>
                    @endif
                    @if($user->admin == 1)
                        <td>&#10004;</td>
                    @else
                        <td></td>
                    @endif
                    <td>
                        <form action="/admin/users/{{ $user->id }}" method="post" class="deleteForm" id="myform">
                            @csrf
                            @method('delete')
                            <div class="btn-group btn-group-sm">
                                @if($user->id == auth()->user()->id)
                                    <button
                                        type="button"
                                        class="btn btn-outline-success"
                                        data-toggle="tooltip"
                                        title="Edit {{ $user->name }}"
                                        disabled="disabled">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @else
                                    <a
                                        href="/" onclick="return false;" class="btn btn-outline-success"
                                        data-toggle="tooltip"
                                        data-name = '{{ $user->name }}'
                                        data-active = '{{ $user->active }}'
                                        data-admin = '{{ $user->admin }}'
                                        data-email = '{{ $user->email }}'
                                        data-id = '{{ $user->id }}'
                                        title="Edit {{ $user->name }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                <button type="button" class="btn btn-outline-danger"
                                        @if($user->id == auth()->user()->id)
                                        disabled
                                        @endif
                                        data-toggle="tooltip"
                                        data-name = '{{ $user->name }}'
                                        data-id = '{{ $user->id }}'
                                        title="Delete {{ $user->name }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
@endsection
@section('script_after')
    <script>
        $(function () {
            $('tbody').on('click', '.btn-outline-danger', function () {
                // Get data attributes from td tag
                let id = $(this).data('id');
                let name = $(this).data('name');
                // Set some values for Noty
                let text = `<p>Delete the user <b>${name}</b>?</p>`;
                let type = 'warning';
                let btnText = 'Delete user';
                let btnClass = 'btn-success';

                // Show Noty
                let modal = new Noty({
                    timeout: false,
                    layout: 'center',
                    modal: true,
                    type: type,
                    text: text,
                    buttons: [
                        Noty.button(btnText, `btn ${btnClass}`, function () {
                            // Delete genre and close modal
                            deleteUser(id);
                            modal.close();
                        }),
                        Noty.button('Cancel', 'btn btn-secondary ml-2', function () {
                            modal.close();
                        })
                    ]
                }).show();
            });
        });
        function deleteUser(id) {
            // Delete the genre from the database
            let pars = {
                '_token': '{{ csrf_token() }}',
                '_method': 'delete'
            };
            $.post(`/admin/users2/${id}`, pars, 'json')
                .done(function (data) {
                    console.log('data', data);
                    new Noty({
                        type: data.type,
                        text: data.text
                    }).show();
                    sleep(3000).then(() => {
                        location.reload();
                    });
                }).fail(function (e) {
                console.log('error', e);
                let msg = '<ul>';
                $.each(e.responseJSON.errors, function (key, value) {
                    msg += `<li>${value}</li>`;
                });
                msg += '</ul>';
                new Noty({
                    type: 'error',
                    text: msg
                }).show();
                sleep(3000).then(() => {
                    location.reload();
                });
            });
        }
        $('tbody').on('click', '.btn-outline-success', function () {
            // Get data attributes from td tag
            let id = $(this).data('id');
            let name = $(this).data('name');
            let email = $(this).data('email');
            let active = $(this).data('active');
            let admin = $(this).data('admin');
            // Update the modal
            if (active == 1) {
              document.getElementById("active").checked = true;
            }else{
                document.getElementById("active").checked = false;
            }
            if (admin == 1) {
                document.getElementById("admin").checked = true;
            }else{
                document.getElementById("admin").checked = false;
            }
            $('.modal-title').text(`Edit ${name}`);
            $('form').attr('action', `/admin/users2/${id}`);
            $('#name').val(name);
            $('#email').val(email);
            $('input[name="_method"]').val('put');
            // Show the modal
            $('#modal-user').modal('show');

            $('#modal-user form').submit(function (e) {
                // Don't submit the form
                e.preventDefault();
                // Get the action property (the URL to submit)
                let action = $(this).attr('action');
                // Serialize the form and send it as a parameter with the post
                let pars = $(this).serialize();
                console.log(pars);
                // Post the data to the URL
                $.post(action, pars, 'json')
                    .done(function (data) {
                        console.log(data);
                        // Noty success message
                        // Hide the modal
                        $('#modal-user').modal('hide');
                        new Noty({
                            type: data.type,
                            text: data.text
                        }).show();
                        // Rebuild the table
                        sleep(3000).then(() => {
                            location.reload();
                        });

                    })
                    .fail(function (e) {
                        console.log('error', e);
                        // e.responseJSON.errors contains an array of all the validation errors
                        console.log('error message', e.responseJSON.errors);
                        // Loop over the e.responseJSON.errors array and create an ul list with all the error messages
                        let msg = '<ul>';
                        $.each(e.responseJSON.errors, function (key, value) {
                            msg += `<li>${value}</li>`;
                        });
                        msg += '</ul>';
                        // Noty the errors
                        new Noty({
                            type: 'error',
                            text: msg
                        }).show();
                    });
            });
        });
        const sleep = (milliseconds) => {
            return new Promise(resolve => setTimeout(resolve, milliseconds))
        }
    </script>
    @include('admin.users2.modal')
@endsection

