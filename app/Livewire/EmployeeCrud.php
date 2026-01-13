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

    public $name;
    public $age;
    public $email;
    public $mobile;
    public $image;
    public $oldImage;
    public $isOpen = false;
    public $employeeId;
    public $isEdit = false;
    public $search;

    public function openModal() {
        $this->isOpen = true;
    }

    public function closeModel() {
       $this->isEdit = false;
         $this->resetForm();
    }

    protected function rules(){
       return  [
        'name' => 'required|max:255',
        'age' => 'required|numeric|min:18',
        'email' => 'required|email|unique:employees,email,'.$this->employeeId,
        'mobile' => 'required|numeric|digits:10'

   ];
    }


    public function updatingSearch(){
        $this->resetPage();
    }

    public function save() {
       $this->validate();

       try{
        $data = $this->only(['name','age','email','mobile']);

         $ext = $this->image->getClientOriginalExtension();
         $imageName = time().'.'.$ext;
         $this->image->storeAs('uploads',$imageName,'public');

         $data['image'] = $imageName;

         Employee::create($data);

         session()->flash('success','Employee created successfully!');
         $this->resetForm();
       }
       catch(\Exception $e) {
        session()->flash('error', $e->getMessage());
       }
    }

    public function edit($id) {
      try{
    $employee = Employee::findOrFail($id);
      $this->employeeId = $id;
      $this->name = $employee->name;
      $this->age = $employee->age;
      $this->email = $employee->email;
      $this->mobile = $employee->mobile;
      $this->oldImage = $employee->image;
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
            $data = $this->only(['name','age','email','mobile']);

            if($this->image) {
                if($this->oldImage) {
                    Storage::disk('public')->delete('uploads/'.$this->oldImage);
                }

            $ext = $this->image->getClientOriginalExtension();
            $imageName = time().'.'.$ext;
            $this->image->storeAs('uploads',$imageName,'public');
            $data['image'] = $imageName;

            }

            $employee->update($data);

            session()->flash('success','Employee Updated Successfully');

            $this->resetForm();


        }
        catch(\Exception $e) {
            session()->flash('error',$e->getMessage());
        }
    }

        public function delete($id){
            try{
            $employee = Employee::findOrFail($id);
            if($employee->image) {
                        Storage::disk('public')->delete('uploads/'. $employee->image);
                    }
            $employee->delete();

            session()->flash('success','Employee Deleted Successfully');
            }
            catch(\Exception $e) {
                session()->flash('error',$e->getMessage());
            }
        }





    public function resetForm() {
        $this->reset(['name','age','email','mobile','isOpen','isEdit','employeeId','image','oldImage']);
    }



    public function render()
    {
        $employees = Employee::query()
        ->where('name','like','%'.$this->search.'%')
        ->orWhere('email','like','%'.$this->search.'%')
        ->orderBy('id','desc')
        ->paginate(10);

        return view('employee-crud',compact('employees'));
    }
}
