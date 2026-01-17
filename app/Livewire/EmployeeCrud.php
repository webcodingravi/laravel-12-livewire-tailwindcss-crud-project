<?php

namespace App\Livewire;

use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class EmployeeCrud extends Component
{
    use WithFileUploads, WithPagination;

    public $name ,$age, $email, $mobile, $image, $status='active';
    public $isOpen = false;
    public $search = '';
    public $isEdit = false;
    public $employeeId;
    public $oldImage;
    public $filterStatus = '';
    public $showTrashed = false;

        public function openModal() {
            $this->isOpen = true;
        }

        public function closeModal() {
            $this->isOpen = false;
            $this->resetForm();
           $this->resetValidation();
        }



         protected function rules() {
            return [
                'name' => 'required|max:255',
                'age' => 'required|numeric|min:18',
                'email'=>'required|email|unique:employees,email,'.$this->employeeId,
                'mobile'=>'required|numeric|digits:10',
                'image' => 'nullable|mimes:jpg,jpeg,png'
            ];
        }

            public function updatingSearch() {
            $this->resetPage();
        }

        public function updatedSearch(){
            $this->resetPage();
        }

        public function save() {
             $this->validate();
            try{

              $data = $this->only(['name','age','email','mobile','status']);

              $ext = $this->image->getClientOriginalExtension();
              $imageName = time().'.'.$ext;
              $this->image->storeAs('uploads',$imageName,'public');

              $data['image'] = $imageName;

                Employee::create($data);

                session()->flash('success','Employee Created Successfully!');
                $this->resetForm();

            }


            catch(\Exception $e) {
                session()->flash('error',$e->getMessage());
            }
        }


        public function edit($id) {

        try{
           $employee = Employee::findOrFail($id);
           $this->name = $employee->name;
           $this->age = $employee->age;
           $this->email = $employee->email;
           $this->mobile = $employee->mobile;
           $this->oldImage = $employee->image;
           $this->status = $employee->status;
           $this->employeeId = $employee->id;
           $this->isEdit = true;
           $this->isOpen = true;
        }

        catch(\Exception $e) {
            session()->flash('error',$e->getMessage());
        }

        }


        public function update() {
            $this->validate();
            try{
              $employee = Employee::findOrFail($this->employeeId);
              $data = $this->only(['name','age','email','mobile','status']);

              if(!empty($this->image)) {
                  if(!empty($this->oldImage)) {
                  Storage::disk('public')->delete('uploads/'.$this->oldImage);
              }

              $ext = $this->image->getClientOriginalExtension();
              $imageName = time().'.'.$ext;
              $this->image->storeAs('uploads',$imageName,'public');
              $data['image'] = $imageName;
              }

              $employee->update($data);



              $this->dispatch('alert',
                type: 'success',
                title: 'Success!',
                text: 'Employee added successfully'
);


              $this->resetForm();

            }

            catch(\Exception $e) {
                session()->flash('error',$e->getMessage());
            }
        }

        public function delete($id) {
            try{

              $employee = Employee::findOrFail($id);


              $employee->delete();

              $this->dispatch('alert',
              type:'success',
              title: 'Success',
              text:'Employee move to trash'
              );
            }
            catch(\Exception $e) {

            $this->dispatch('alert',
              type:'error',
              title: 'Error!',
              text:$e->getMessage()
              );
            }
        }


        public function restore($id) {
          try{
             Employee::onlyTrashed()
            ->findOrFail($id)
            ->restore();

              $this->dispatch('alert',
              type:'success',
              title: 'Success',
              text:'Employee Restored Successfully'
              );

          }
          catch(\Exception $e) {
             $this->dispatch('alert',
              type:'error',
              title: 'Error!',
              text:$e->getMessage()
              );
          }
        }

        public function forceDelete($id) {
            try {
              $employee = Employee::onlyTrashed()->findOrFail($id);

               if(!empty($employee->image)) {
                Storage::disk('public')->delete('uploads/'.$employee->image);
              }

              $employee->forceDelete();


              $this->dispatch('alert',
              type:'success',
              title: 'Success',
              text:'Employee permanently deleted'
              );

            }
            catch(\Exception $e) {
                $this->dispatch('alert',
              type:'error',
              title: 'Error!',
              text:$e->getMessage()
              );
            }
        }

        public function resetForm() {
            $this->reset(['name','age','email','mobile', 'image','isOpen','isEdit','employeeId','oldImage','status']);
        }




    public function render()
    {
        $employees = Employee::query()
        ->when($this->showTrashed, function($query) {
            $query->onlyTrashed();
        })
        ->when(!$this->showTrashed,function($query){
            $query->where('deleted_at',null);
        } )
        ->when($this->search,function($query) {
            $query->where('name','like','%'.$this->search.'%')
        ->orWhere('email','like','%'.$this->search.'%');
        })

        ->when(!empty($this->filterStatus),function($query) {
              $query->where('status',$this->filterStatus);
        })
        ->orderBy('id','desc')
        ->paginate(4);


        return view('employee-crud',compact('employees'));
    }
}