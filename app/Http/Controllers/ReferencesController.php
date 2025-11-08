<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReferencesController extends Controller
{
    public function index(){
        $data = Reference::query();
        if (Auth::user()->type == 0) {
            $data->where('user_id', Auth::id());
        }
        $data = $data->with('patient')->get()->toArray();
        
        return view('backend.reference.index',compact('data'));
    }

    public function create(){
        return view('backend.reference.create');
    }
    public function store(Request $request){
        $rules = [
            'name' => 'required|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $data = $request->only(['name', 'phone', 'code']);
            $data['user_id'] = Auth::user()->id;
            Reference::create($data);
            return redirect()->route('reference.index');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while storing data: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function edit($id){
        $data = Reference::where('id',$id)->first();
        return view('backend.reference.edit',compact('data'));
    }
    public function destroy($id){
        Reference::where('id',$id)->delete();
        return redirect()->route('reference.index');
    }
    public function update(Request $request, $id){
        $rules = [
            'name' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->only(['name', 'phone', 'code']);
            Reference::where('id',$id)->update($data);
            return redirect()->route('reference.index')->with('success', 'Contact updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while updating data: ' . $e->getMessage())
                ->withInput();
        }
    }
}
