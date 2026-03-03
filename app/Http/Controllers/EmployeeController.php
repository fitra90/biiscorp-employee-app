<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employees.index');
    }

    // API: DataTables server-side
    public function apiIndex(Request $request)
    {
        $query = Employee::query();

        // Global search
        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('department', 'like', "%$search%")
                  ->orWhere('position', 'like', "%$search%");
            });
        }

        // Date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('join_date', [
                $request->date_from,
                $request->date_to
            ]);
        }

        // Department filter
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Individual Column Search
        $columns = ['id', 'name', 'email', 'position', 'department', 'join_date'];
        foreach ($request->input('columns', []) as $key => $column) {
            if (!empty($column['search']['value'])) {
                $field = $columns[$key] ?? null;
                if ($field) {
                    if ($field === 'department' || $field === 'position') {
                        $query->where($field, $column['search']['value']);
                    } else {
                        $query->where($field, 'like', '%' . $column['search']['value'] . '%');
                    }
                }
            }
        }

        $total = Employee::count();
        $filtered = $query->count();

        // Ordering
        $columns = ['id', 'name', 'email', 'position', 'department', 'join_date'];
        $orderCol = $columns[$request->input('order.0.column', 0)] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');
        $query->orderBy($orderCol, $orderDir);

        // Pagination
        $data = $query->offset($request->input('start', 0))
                      ->limit($request->input('length', 10))
                      ->get();

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }

    // API: Store new employee
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:employees',
            'phone'      => 'nullable|string|max:20',
            'position'   => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'join_date'  => 'required|date',
            'photo'      => 'nullable|image|max:2048',
            'document'   => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        if ($request->hasFile('document')) {
            $validated['document'] = $request->file('document')->store('documents', 'public');
        }

        $employee = Employee::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully',
            'data'    => $employee
        ], 201);
    }

    public function apiList()
    {
        return response()->json([
            'success' => true,
            'data'    => Employee::all()
        ]);
    }
}