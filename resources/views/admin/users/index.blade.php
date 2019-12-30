@extends('layouts.template')

@section('title', 'Users')

@section('main')
    <h1>Users</h1>
    <form method="get" action="/admin/users" id="searchForm">
        <div class="row">
            <div class="col-sm-7 mb-2">
                Filter Name Or Email
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name"
                       value="{{ request()->name }}" placeholder="Filter Name Or Email">
                @error('name')
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
                                        disabled="disabled"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @else
                                    <a
                                        href="/admin/users/{{ $user->id }}/edit" class="btn btn-outline-success"
                                        data-toggle="tooltip"
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
        // // $(function () {
        // //     loadTable();
        // // });
        //
        // // Load genres with AJAX
        // function loadTable() {
        //     $.getJSON('/admin/users/qryUsers')
        //         .done(function (data) {
        //             console.log('data', data);
        //             // Clear tbody tag
        //             $('tbody').empty();
        //             // Loop over each item in the array
        //             $.each(data, function (key, value) {
        //                 $active = '';
        //                 if(value.active ==1){
        //                     $active ='&#10004;'
        //                 }
        //                 $admin = '';
        //                 if(value.admin ==1){
        //                     $admin ='&#10004;'
        //                 }
        //                 let tr = `<tr>
        //                        <td>${value.id}</td>
        //                        <td>${value.name}</td>
        //                        <td>${value.email}</td>
        //                        <td>${$active}</td>
        //                        <td>${$admin}</td>
        //                        <td>
        //                             <div class="btn-group btn-group-sm">
        //                                 <a href="/admin/users/${value.id}/edit" class="btn btn-outline-success btn-edit" data-toggle="tooltip"
        //                                 title="Edit ${value.name}">
        //                                     <i class="fas fa-edit"></i>
        //                                 </a>
        //                                 <button type="button" class="btn btn-outline-danger"
        //                                 data-toggle="tooltip"
        //                                 data-name = '${value.name}'
        //                                 data-id = '${value.id}'
        //                                 title="Delete ${value.name}">
        //                                     <i class="fas fa-trash-alt"></i>
        //                                 </button>
        //                             </div>
        //                        </td>
        //                    </tr>`;
        //                 // Append row to tbody
        //                 $('tbody').append(tr);
        //             });
        //         })
        //         .fail(function (e) {
        //             console.log('error', e);
        //         })
        // }
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
            $.post(`/admin/users/${id}`, pars, 'json')
                .done(function (data) {
                    console.log('data', data);
                    location.reload();
                }).fail(function (e) {
                console.log('error', e);
                location.reload();
            });
        }
        $(document).ready(function (){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
