<?php

namespace App\Http\Controllers\User;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserImportRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Imports\UserImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all()->select('id', 'name');
        return view('users.index', compact('users'));
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::latest('id');
            if ($request->user_id) {
                $data->where('id', $request->user_id);
            }
            $data = $data->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function ($row) {
                    return '<a href="' . asset('storage/' . $row->image) . '" target="_blank">
                                <img src="' . asset('storage/' . $row->image) . '" width="50" height="50" alt="User Image">
                            </a>';
                })
                ->editColumn('created_at', function ($row) {
                    return date_format($row->created_at, 'd-m-Y');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('users.form', $row->id) . '" class="editUserForm btn btn-success btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="deleteUser btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }
    }

    public function form($id = null)
    {
        $user = $id ? User::find($id) : null;
        return view('users.createUpdate', compact('user'));
    }

    public function storeOrUpdate(UserUpdateRequest $request)
    {
        $data = $request->all();

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('avatar', 'public');
            $data['image'] = $filePath;
        }

        if (isset($data['user_id'])) {
            $user = User::findOrFail($data['user_id']);
            $user->update($data);
            $message = 'User updated successfully';
        } else {
            User::create($data);
            $message = 'User created successfully';
        }

        return response()->json(['success' => $message]);
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return response()->json(['success' => 'User deleted successfully']);
    }

    public function import(UserImportRequest $request)
    {
        $file = $request->file('file');
        Excel::import(new UserImport, $file);

        return response()->json(['success' => 'User imported successfully']);
    }

    public function export()
    {
        return Excel::download(new UserExport(), 'users.xlsx');
    }
}
