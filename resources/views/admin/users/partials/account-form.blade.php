@php $p = $prefix ?? ''; $isEdit = $edit ?? false; @endphp
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" id="{{ $p }}name" class="form-control" required>
</div>
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" id="{{ $p }}email" class="form-control" required>
</div>
<div class="mb-3">
    <label class="form-label">Mobile</label>
    <input type="text" name="mobile" id="{{ $p }}mobile" class="form-control">
</div>
<div class="mb-3">
    <label class="form-label">Password{{ $isEdit ? ' (leave blank to keep current)' : '' }}</label>
    <input type="password" name="password" class="form-control" {{ $isEdit ? '' : 'required' }}>
</div>
<div class="mb-3">
    <label class="form-label">Role</label>
    <select name="role" id="{{ $p }}role" class="form-control" required>
        <option value="admin">Admin</option>
        <option value="accountant" selected>Accountant</option>
        <option value="viewer">Viewer</option>
    </select>
</div>
<div class="mb-3">
    <input type="hidden" name="status" value="0">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="status" id="{{ $p }}status" value="1" {{ $isEdit ? '' : 'checked' }}>
        <label class="form-check-label" for="{{ $p }}status">Active</label>
    </div>
</div>
