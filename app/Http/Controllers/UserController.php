<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Requests\ListRequest;
use App\Http\Requests\ReadRequest;
use App\Http\Requests\CreateRequest;
use App\Http\Requests\UpdateRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function list(ListRequest $request)
    {
        $orWhere_columns = [
            'username'
        ];

        $key = ($request->search_key) ? $request->search_key : '';

        if ($request->search_key) {
            $key = $request->search_key;
        }

        $limit = ($request->limit) ? $request->limit : 50;
        $sort_column = ($request->sort_column) ? $request->sort_column : 'created_at';
        $sort_order = ($request->sort_order) ? $request->sort_order : 'desc';

        $data = $this->user->where(function ($q) use ($orWhere_columns, $key) {
            foreach ($orWhere_columns as $column) {
                $q->orWhere($column, 'LIKE', "%{$key}%");
            }
        });

        if ($request->from && $request->to) {
            $data = $data->whereBetween('created_at', [
                Carbon::parse($request->from)->format('Y-m-d H:i:s'),
                Carbon::parse($request->to)->format('Y-m-d H:i:s')
            ]);
        }

        $data = $data->orderBy($sort_column, $sort_order)->paginate($limit);

        return response()->json([
            'data' => $data,
            'status' => 'sucess'
        ]);
    }

    public function create(CreateRequest $request)
    {
        $request['password'] = Hash::make($request->password);

        $this->user->create($request->all());

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function read(ReadRequest $request)
    {
        $data = $this->user->find($this->encryptDecrypt($request->id, 'decrypt'));

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unprocessable ID',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->safe()->all();

        $data = $this->user->find($this->encryptDecrypt($validated['id'], 'decrypt'));

        $data->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
