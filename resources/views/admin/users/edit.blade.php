<x-layouts.admin title="Edit User">

<div style="max-width:560px;">
    <x-ui.breadcrumb :items="[['label'=>'Users','href'=>route('admin.users.index')],['label'=>$user->name]]" style="margin-bottom:20px;" />

    @if(session('success'))
    <x-ui.alert variant="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    @if(!empty($errors) && $errors->any())
    <x-ui.alert class="mb-4">{{ $errors->first() }}</x-ui.alert>
    @endif

    <x-ui.card title="Edit User">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div style="display:flex; flex-direction:column; gap:16px;">
                <x-ui.input name="name"  label="Full Name" :value="old('name', $user->name)"   :error="$errors->first('name')"  required />
                <x-ui.input name="email" label="Email"      type="email" :value="old('email', $user->email)" :error="$errors->first('email')" required />
                <x-ui.input name="password" label="New Password" type="password" placeholder="Leave blank to keep current" :error="$errors->first('password')" />

                <x-ui.select
                    name="role"
                    label="Role"
                    :options="$roles->pluck('name','name')->toArray()"
                    :selected="old('role', $user->getRoleNames()->first())"
                    :error="$errors->first('role')"
                />

                <x-ui.separator />

                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <x-ui.button href="{{ route('admin.users.index') }}" tag="a" variant="secondary">Cancel</x-ui.button>
                    <x-ui.button type="submit">Save Changes</x-ui.button>
                </div>
            </div>
        </form>
    </x-ui.card>
</div>

</x-layouts.admin>
