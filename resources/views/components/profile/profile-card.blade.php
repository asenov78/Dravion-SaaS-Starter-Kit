@php $u = auth()->user(); @endphp
<div>
    <div class="mb-6 rounded-2xl border border-gray-200 p-5 lg:p-6 dark:border-gray-800">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex w-full flex-col items-center gap-6 xl:flex-row">
                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-500 text-2xl font-semibold text-white">
                    @if($u->avatar)
                        <img src="{{ Storage::url($u->avatar) }}" alt="{{ $u->name }}" class="h-full w-full object-cover">
                    @else
                        {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
                    @endif
                </div>
                <div class="order-3 xl:order-2">
                    <h4 class="mb-2 text-center text-lg font-semibold text-gray-800 xl:text-left dark:text-white/90">
                        {{ $u->name }}
                    </h4>
                    <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $u->bio ?: ucfirst(optional($u->roles->first())->name ?? 'Member') }}
                        </p>
                        <div class="hidden h-3.5 w-px bg-gray-300 xl:block dark:bg-gray-700"></div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ trim(($u->city_state ? $u->city_state.', ' : '').($u->country ?? '')) ?: $u->email }}
                        </p>
                    </div>
                </div>
            </div>

            <button @click="$dispatch('open-profile-info-modal')"
                class="shadow-theme-xs flex w-full items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 lg:inline-flex lg:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="" />
                </svg>
                Edit
            </button>
        </div>
    </div>

    <!-- Profile Info Modal -->
    <x-ui.modal x-data="{ open: false }" @open-profile-info-modal.window="open = true" :isOpen="false" class="max-w-[700px]">
        <div class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
            <div class="px-2 pr-14">
                <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Edit Personal Information</h4>
                <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">Update your details to keep your profile up-to-date.</p>
            </div>
            <form method="POST" action="{{ route('admin.ui.profile.update') }}" enctype="multipart/form-data" class="flex flex-col">
                @csrf
                @method('PUT')
                <div class="custom-scrollbar h-[458px] overflow-y-auto p-2">
                    {{-- Avatar upload --}}
                    <div class="mb-7">
                        <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">Profile Photo</h5>
                        <div class="flex items-center gap-5">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-full bg-brand-500 flex items-center justify-center text-xl font-semibold text-white">
                                @if($u->avatar)
                                    <img src="{{ Storage::url($u->avatar) }}" alt="{{ $u->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <input type="file" name="avatar" accept="image/*" id="avatar-input"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 dark:text-gray-400 dark:file:bg-brand-500/10 dark:file:text-brand-400">
                                <p class="mt-1 text-xs text-gray-400">JPG, PNG, GIF — max 2 MB</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">Social Links</h5>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                            @foreach (['facebook' => 'Facebook', 'x_url' => 'X.com', 'linkedin' => 'Linkedin', 'instagram' => 'Instagram'] as $field => $label)
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</label>
                                    <input type="text" name="{{ $field }}" value="{{ old($field, $u->$field) }}"
                                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-7">
                        <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">Personal Information</h5>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                            <div class="col-span-2 lg:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                                <input type="text" name="name" value="{{ old('name', $u->name) }}"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                            </div>
                            <div class="col-span-2 lg:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $u->email) }}"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                            </div>
                            <div class="col-span-2 lg:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $u->phone) }}"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                            </div>
                            <div class="col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Bio</label>
                                <input type="text" name="bio" value="{{ old('bio', $u->bio) }}"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">Close</button>
                    <button type="submit"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">Save Changes</button>
                </div>
            </form>
        </div>
    </x-ui.modal>
</div>
