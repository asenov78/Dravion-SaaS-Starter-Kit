<x-layouts.admin title="New User">

<div style="max-width:560px;">
    <x-ui.breadcrumb :items="[['label'=>'Users','href'=>route('admin.users.index')],['label'=>'New User']]" style="margin-bottom:20px;" />

    @if($errors->any())
    <x-ui.alert class="mb-4">{{ $errors->first() }}</x-ui.alert>
    @endif

    <x-ui.card title="Create User">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div style="display:flex; flex-direction:column; gap:16px;">
                <x-ui.input name="name"  label="Full Name"  :value="old('name')"  :error="$errors->first('name')"  required />
                <x-ui.input name="email" label="Email"       type="email" :value="old('email')" :error="$errors->first('email')" required />
                <x-ui.input name="password" label="Password" type="password" :error="$errors->first('password')" required />

                <x-ui.select
                    name="role"
                    label="Role"
                    :options="$roles->pluck('name','name')->toArray()"
                    :selected="old('role')"
                    placeholder="Select a role"
                    :error="$errors->first('role')"
                />

                <x-ui.separator />

                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <x-ui.button href="{{ route('admin.users.index') }}" tag="a" variant="secondary">Cancel</x-ui.button>
                    <x-ui.button type="submit">Create User</x-ui.button>
                </div>
            </div>
        </form>
    </x-ui.card>
</div>

</x-layouts.admin>
