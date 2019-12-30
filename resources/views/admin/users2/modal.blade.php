<div class="modal" id="modal-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">modal-user-title</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/admin/users2/{{ $user->id }}" method="post">
                    @method('')
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name"
                               class="form-control"
                               placeholder="Name"
                               minlength="3"
                               required
                               value="">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email"
                               class="form-control"
                               placeholder="Email"
                               minlength="3"
                               required
                               value="">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value='1'>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="admin" name="admin" value='1'>
                            <label class="form-check-label" for="admin">Admin</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-2">Save user</button>
                </form>
            </div>
        </div>
    </div>
</div>
