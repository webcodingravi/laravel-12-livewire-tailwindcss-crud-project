<div>

    <div class="min-h-screen bg-slate-100 flex items-center justify-center flex-col gap-8">
        <x-alertMessage />
        <div class="w-6/12 bg-white p-4 rounded">
            <div class="flex items-center justify-end gap-4 mb-4">
                <input type="search" placeholder="Search..." wire:model.live.debounce.300ms="search"
                    class="border border-slate-200 p-3 rounded focus:outline-none w-3/12">

                <button wire:click="openModal"
                    class="px-5 bg-indigo-500 active:scale-90 transition-all duration-300 cursor-pointer py-3 text-white font-medium rounded">Add
                    Employee</button>
            </div>


            <table class="w-full border-collapse text-left mb-4">
                <tr class="bg-slate-200">
                    <th class="p-4">S.No.</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Action</th>
                </tr>
                <tbody>
                    @if ($employees->isNotEmpty())
                        @foreach ($employees as $key => $employee)
                            <tr class="border-b border-b-slate-200">
                                <td class="p-9">{{ ++$key }}</td>
                                <td>
                                    <img src="{{ asset('storage/uploads/' . $employee->image) }}" alt="Employee Image"
                                        class="w-16 h-16 object-cover rounded">
                                </td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->age }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->mobile }}</td>
                                <td class="flex items-center gap-4 mt-7">
                                    <button wire:click="edit({{ $employee->id }})"
                                        class="px-4 py-2 bg-indigo-500 rounded active:scale-90 transition-all duration-300 cursor-pointer text-white">Edit</button>
                                    <button wire:click="delete({{ $employee->id }})" onclick="confirm('Are you sure?')"
                                        class="px-4 py-2 bg-rose-500 rounded active:scale-90 transition-all duration-300 cursor-pointer text-white">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="p-4 text-center">No Employees Found.</td>
                        </tr>

                    @endif
                </tbody>

            </table>

            {{ $employees->links() }}
        </div>



    </div>

    <!-- Modal -->
    @if ($isOpen)
        <div class="fixed inset-0 bg-black/50  flex items-center justify-center animate__animated animate__fadeIn">
            <x-alertMessage />
            <div class="bg-white p-6 rounded animate__animated animate__zoomIn w-6/12 shadow-lg">
                <button class="p-4 absolute right-2 top-1 cursor-pointer text-xl" wire:click="closeModel">x</button>
                <h2 class="text-xl font-bold mb-4">{{ $isEdit ? 'Edit Employee' : 'Add Employee' }}</h2>

                <hr class="text-slate-200 my-4">

                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}" class="w-full flex flex-col gap-8">
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-800">Name</label>
                        <input type="text" class="border border-slate-200 p-4 focus:outline-none rounded"
                            placeholder="Enter Name..." wire:model.differ="name">
                        @error('name')
                            <span class="text-rose-500 text-sm">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-800">Age</label>
                        <input type="text" class="border border-slate-200 p-4 focus:outline-none rounded"
                            placeholder="Enter Age..." wire:model.differ="age">
                        @error('age')
                            <span class="text-rose-500 text-sm">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-800">Email</label>
                        <input type="text" class="border border-slate-200 p-4 focus:outline-none rounded"
                            placeholder="Enter Email..." wire:model.differ="email">
                        @error('email')
                            <span class="text-rose-500 text-sm">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>


                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-800">Mobile</label>
                        <input type="text" class="border border-slate-200 p-4 focus:outline-none rounded"
                            placeholder="Enter Mobile..." wire:model.differ="mobile">
                        @error('mobile')
                            <span class="text-rose-500 text-sm">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>


                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-800">Upload Image</label>
                        <input type="file"
                            class="border border-slate-200 p-4 focus:outline-none rounded cursor-pointer"
                            accept="image/*" wire:model.differ="image">
                        @error('image')
                            <span class="text-rose-500 text-sm">
                                {{ $message }}
                            </span>
                        @enderror

                        {{-- Image Preview: --}}

                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="mt-2 w-40 object-cover">
                        @elseif($oldImage)
                            <img src="{{ Storage::url('uploads/' . $oldImage) }}" alt="Preview"
                                class="mt-2 w-40 object-cover">
                        @endif

                    </div>


                    <div class="flex items-center justify-end gap-4">

                        @if ($isEdit)
                            <button type="button" wire:click="closeModel"
                                class="px-5 bg-slate-500 active:scale-90 transition-all duration-300 cursor-pointer py-3 text-white font-medium rounded">Cancel</button>
                        @endif

                        <button type="submit"
                            class="px-5 bg-indigo-500 active:scale-90 transition-all duration-300 cursor-pointer py-3 text-white font-medium rounded">{{ $isEdit ? 'Update' : 'save' }}</button>



                </form>

            </div>

        </div>
    @endif


</div>
