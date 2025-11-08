<?php

namespace App\Http\Controllers;

use App\Models\MedicalTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MedicalTestController extends Controller
{
    public function index()
    {
        $data = MedicalTest::with('invoice')->get()->toArray();
        return view('backend.medical-test.index', compact('data'));
    }

    // Show the form for creating a new medical test
    public function create()
    {
        return view('backend.medical-test.create');
    }

    // Store a newly created medical test in storage
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:medical_tests,name',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            MedicalTest::create($request->all());
            return redirect()->route('medical-test.index')->with('success', 'Medical test created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while storing data: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Show the form for editing a specific medical test
    public function edit($id)
    {
        $data = MedicalTest::findOrFail($id);
        return view('backend.medical-test.edit', compact('data'));
    }

    // Update a specific medical test
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('medical_tests', 'name')->ignore($id),
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->only(['name', 'price']);
            MedicalTest::where('id', $id)->update($data);
            return redirect()->route('medical-test.index')->with('success', 'Medical test updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while updating data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            MedicalTest::where('id', $id)->delete();
            return redirect()->route('medical-test.index')->with('success', 'Medical test deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while deleting data: ' . $e->getMessage());
        }
    }
}
