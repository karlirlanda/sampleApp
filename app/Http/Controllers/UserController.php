<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\EncryptDecrypt;
use App\Http\Requests\ListRequest;
use App\Http\Requests\ReadRequest;
use Illuminate\Support\Facades\DB;
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

        $user = $this->user->create($request->all());

        $user['e'] = 1;

        event(new UserCreated($user));

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function insertBatch(Request $request)
    {

        $data = [
            ['username' => 'karl', 'password' => Hash::make('123456')],
            ['username' => 'ricardo', 'password' => Hash::make('123456')],
            ['username' => 'brandon', 'password' => Hash::make('123456')],
        ];

        User::insert($data);

        // Get the IDs of the inserted rows
        $ids = DB::table('users')->whereIn('username', array_column($data, 'username'))
            ->pluck('id')
            ->toArray();

        $ids['e'] = 2;

        event(new UserCreated($ids));

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function read(ReadRequest $request)
    {
        $validated = $request->safe()->all();

        $id = EncryptDecrypt::encryptDecrypt($validated['id'], 'decrypt');

        $data = $this->user->find($id);

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

        $id = EncryptDecrypt::encryptDecrypt($validated['id'], 'decrypt');

        $data = $this->user->find($id)->update($validated);


        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }
}
