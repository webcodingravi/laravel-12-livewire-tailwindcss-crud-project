<div>
    <div class="h-screen  flex items-center justify-center flex-col gap-6">
        <x-alertmessage />
        <div class="rounded bg-white shadow p-4 w-8/12">
            <div class="flex items-center justify-end gap-4 mb-4">
                <select class="px-6 py-3 border border-slate-200 rounded-md focus:outline-none cursor-pointer"
                    wire:model.live="filterStatus">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="deactive">Deactive</option>
                </select>
                <input type="search" placeholder="Search..."
                    class="p-4 rounded border border-slate-200 w-3/12 focus:outline-none"
                    wire:model.live.debounce.300ms="search" />
                <button
                    class="text-white bg-indigo-600 px-6 py-4 rounded active:scale-90 duration-300 transition-all cursor-pointer"
                    wire:click="openModal">Add
                    Employee</button>
            </div>

            <table class="w-full mb-4">
                <tr class="bg-indigo-500 text-white font-semibold">
                    <td class="p-4">S.NO.</td>
                    <td>Image</td>
                    <td>Name</td>
                    <td>Age</td>
                    <td>Emal</td>
                    <td>Mobile</td>
                    <td>Status</td>
                    <td>Action</td>
                </tr>

                <tbody>
                    @if ($employees->isNotEmpty())
                        @foreach ($employees as $employee)
                            <tr class="border-b border-b-slate-200">
                                <td class="p-7">
                                    {{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <img src="{{ asset('storage/uploads/' . $employee->image) }}" alt="Employee Image"
                                        class="w-16 h-16 object-cover rounded">
                                </td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->age }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->mobile }}</td>
                                <td>
                                    @if ($employee->status == 'active')
                                        <span class="bg-green-200 p-2 rounded-md text-green-800 text-sm font-medium">
                                            Active
                                        </span>
                                    @else
                                        <span class="bg-rose-200 p-2 rounded-md text-rose-800 text-sm font-medium">
                                            Deactive
                                        </span>
                                    @endif

                                </td>
                                <td class="space-x-4">
                                    <button
                                        class="text-white px-4 py-2 active:scale-90 transition-all duration-300 bg-indigo-500 rounded cursor-pointer"
                                        wire:click="edit(({{ $employee->id }}))">Edit</button>
                                    <button
                                        class="text-white px-4 py-2 active:scale-90 transition-all duration-300 bg-red-500 rounded cursor-pointer"
                                        onclick="confirm('Are you sure?')"
                                        wire:click="delete({{ $employee->id }})">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center p-4">No Employees Found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            {{ $employees->links() }}
        </div>
    </div>


    @if ($isOpen)
        <div
            class="fixed bg-black/50 inset-0 flex items-center justify-center flex-col animate__animated animate__fadeIn">
            <div class="bg-white w-8/12 rounded shadow-md relative animate__animated animate__zoomIn">
                <h1 class="text-2xl font-bold p-4">{{ $isEdit ? 'Edit Employee' : 'Add Employee' }}</h1>
                <button class="absolute top-3 right-4 text-2xl cursor-pointer" wire:click="closeModal">x</button>

                <hr class="text-slate-200 my-4 w-full" />

                <form class="w-full p-4 flex flex-col gap-8" wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}">
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-600">Name</label>
                        <input type="text" class="border border-slate-300 rounded p-4 focus:outline-none w-full"
                            placeholder="Enter Name..." wire:model.deffer="name">
                        @error('name')
                            <span class="text-sm text-rose-500">{{ $message }}</span>
                        @enderror

                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-600">Age</label>
                        <input type="number" class="border border-slate-300 rounded p-4 focus:outline-none w-full"
                            placeholder="Enter Age..." wire:model.deffer="age">
                        @error('age')
                            <span class="text-sm text-rose-500">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-600">Email</label>
                        <input type="text" class="border border-slate-300 rounded p-4 focus:outline-none w-full"
                            placeholder="Enter Email..." wire:model.deffer="email">
                        @error('email')
                            <span class="text-sm text-rose-500">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-600">Mobile</label>
                        <input type="text" class="border border-slate-300 rounded p-4 focus:outline-none w-full"
                            placeholder="Enter Mobile..." wire:model.deffer="mobile">
                        @error('mobile')
                            <span class="text-sm text-rose-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-600">Status</label>
                        <select class="border border-slate-300 rounded p-4 focus:outline-none w-full"
                            wire:model.deffer="status">
                            <option value="active">Active</option>
                            <option value="deactive">Deactive</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-600">Upload Image</label>
                        <input type="file"
                            class="border border-slate-300 rounded p-4 focus:outline-none w-full cursor-pointer"
                            wire:model.deffer="image" accept="image/*">
                        @error('image')
                            <span class="text-sm text-rose-500">{{ $message }}</span>
                        @enderror


                        {{-- preview Image --}}

                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" alt="" class="object-cover mt-2 w-40">
                        @elseif($oldImage)
                            <img src="{{ asset('storage/uploads/' . $oldImage) }}" alt=""
                                class="object-cover mt-2 w-40">
                        @endif
                    </div>


                    <div class="flex items-center justify-end">

                        <button type="submit"
                            class="px-6 py-4 bg-indigo-600 active:scale-90 duration-300 transiton-all text-white cursor-pointer rounded w-fit">

                            <span>
                                {{ $isEdit ? 'Update' : 'Save' }}
                            </span>
                        </button>
                    </div>
                </form>


            </div>
        </div>
    @endif


</div>
