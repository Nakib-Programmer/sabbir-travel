<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function index(){
        $data = Contact::get()->toArray();
        return view('backend.contacts.index',compact('data'));
    }

    public function create(){
        return view('backend.contacts.create');
    }
    public function store(Request $request){
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|unique:contacts,phone',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            Contact::create($request->all());
            return redirect()->route('contact.index');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while storing data: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function edit($id){
        $data = Contact::where('id',$id)->first();
        return view('backend.contacts.edit',compact('data'));
    }
    public function destroy($id){
        Contact::where('id',$id)->delete();
        return redirect()->route('contact.index');
    }
    public function update(Request $request, $id){
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                Rule::unique('contacts', 'phone')->ignore($id),
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->only(['name', 'phone', 'organization','designation','note']);
            Contact::where('id',$id)->update($data);
            return redirect()->route('contact.index')->with('success', 'Contact updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while updating data: ' . $e->getMessage())
                ->withInput();
        }
    }
}
