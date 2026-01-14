<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    protected function getNotFoundError(string $id) {
        return "Task {$id} is not found";
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => ['required', 'max:256','string'],
                'description' => ['nullable', 'max:12000','string'],
                'status' => ['required', Rule::in('active', 'finished')]
            ]
        );
        
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $task = new Task();
        $task->fill($validator->validated());
        $task->save();

        return response(["message" => "Task {$task->id} is created"], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $task = Task::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'message' => $this->getNotFoundError($id),
            ], 404);
        }

        return $task;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => ['sometimes','required', 'max:256', 'string'],
                'description' => ['sometimes','required', 'max:12000', 'string'],
                'status' => ['sometimes', 'required', Rule::in('active', 'finished')]
            ]
        );
        
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        try {
            $task = Task::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'message' => $this->getNotFoundError($id),
                404
            ]);
        }
    
        $validated = $validator->validated();

        $task->update($validated);
        $task->save();

        return response(["message" => "Task {$id} is updated"], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $task = Task::findOrFail($id);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'message' => $this->getNotFoundError($id),
            ], 404);
        }

        $task->delete();

        return response(['message' => "Task {$id} is deleted"], 200);
    }
}
