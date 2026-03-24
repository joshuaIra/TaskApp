@csrf

<div class="mb-4">
    <label class="block text-gray-700">Name</label>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="w-full border rounded px-3 py-2" required>
</div>

<div class="mb-4">
    <label class="block text-gray-700">Department</label>
    @php
        $departments = ['ICT', 'Big Data', 'SMRP', 'DSS', 'DES', 'Census', 'Finance', 'DDG Office', 'DG office', 'HCS', 'HR', 'Procurement', 'SPIU Office'];
    @endphp
    <select name="department" class="w-full border rounded px-3 py-2" required>
        <option value="" disabled {{ old('department', $user->department ?? '') ? '' : 'selected' }}>Select department</option>
        @foreach($departments as $department)
            <option value="{{ $department }}" {{ old('department', $user->department ?? '') === $department ? 'selected' : '' }}>{{ $department }}</option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label class="block text-gray-700">Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="w-full border rounded px-3 py-2" required>
</div>

<div class="mb-4">
    <label class="block text-gray-700">Role</label>
    <select name="role" class="w-full border rounded px-3 py-2" required>
        @foreach(['admin','manager','member'] as $role)
            <option value="{{ $role }}" {{ (old('role', $user->role ?? '') === $role) ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} class="form-checkbox">
        <span class="ml-2">Active</span>
    </label>
</div>

<div class="mb-4">
    <label class="block text-gray-700">Password {{ isset($user) ? '(leave blank to keep current)' : '' }}</label>
    <input type="password" name="password" class="w-full border rounded px-3 py-2" {{ isset($user) ? '' : 'required' }}>
</div>

<div class="mb-4">
    <label class="block text-gray-700">Confirm Password</label>
    <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" {{ isset($user) ? '' : 'required' }}>
</div>