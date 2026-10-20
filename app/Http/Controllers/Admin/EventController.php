<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Member;
use App\Models\Category;

class EventController extends Controller
{
    public function index() { 
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->route('admin.dashboard')->with('error', 'Belum ada periode yang aktif. Silakan aktifkan salah satu periode terlebih dahulu.');
        }

        $events = Event::with(['members.division', 'categories'])->where('period_id', $activePeriod->id)->latest('event_date')->paginate(15);
        $allEvents = Event::with(['members.division', 'categories'])->where('period_id', $activePeriod->id)->get();
        $divisions = \App\Models\Division::all()->keyBy('id');

        $allEvents->transform(function($event) use ($divisions) {
            $event->members->transform(function($member) use ($divisions) {
                $pivotDivisionId = $member->pivot->division_id ?? $member->division_id;
                $member->pivot_division = $divisions->get($pivotDivisionId);
                $colors = [
                    'Inti' => '#8b5cf6',
                    'Creative' => '#ec4899',
                    'Programmer' => '#3b82f6',
                    'Media' => '#f59e0b',
                ];
                $divName = $member->pivot_division ? $member->pivot_division->name : '';
                $member->color = $colors[$divName] ?? '#64748b';
                return $member;
            });
            $event->formatted_time = $event->event_time ? $event->event_time->format('H:i') : null;
            return $event;
        });

        $categories = Category::all();
        $members = Member::with('division')->where('period_id', $activePeriod->id)->get();

        $customColumns = \App\Models\Setting::get('event_custom_columns', []);
        if (is_string($customColumns)) {
            $customColumns = json_decode($customColumns, true) ?? [];
        }

        $defaultColumns = [
            ['id' => 'title', 'name' => 'Judul Kegiatan', 'type' => 'standard', 'width' => 'min-w-[240px]'],
            ['id' => 'event_date', 'name' => 'Tanggal', 'type' => 'standard', 'width' => 'min-w-[140px]'],
            ['id' => 'event_time', 'name' => 'Waktu', 'type' => 'standard', 'width' => 'min-w-[100px]'],
            ['id' => 'status', 'name' => 'Status', 'type' => 'standard', 'width' => 'min-w-[130px]'],
            ['id' => 'category_id', 'name' => 'Kategori', 'type' => 'standard', 'width' => 'min-w-[150px]'],
            ['id' => 'member_id', 'name' => 'Petugas Utama', 'type' => 'standard', 'width' => 'min-w-[180px]'],
        ];

        $allColumns = $defaultColumns;
        foreach ($customColumns as $col) {
            $allColumns[] = [
                'id' => $col['id'],
                'name' => $col['name'],
                'type' => 'custom',
                'col_type' => $col['type'] ?? 'text',
                'options' => $col['options'] ?? '',
                'width' => 'min-w-[160px]'
            ];
        }

        $columnOrder = \App\Models\Setting::get('event_table_columns_order', []);
        if (is_string($columnOrder)) {
            $columnOrder = json_decode($columnOrder, true) ?? [];
        }

        if (!empty($columnOrder)) {
            $orderedColumns = [];
            $allColumnsKeyed = collect($allColumns)->keyBy('id');
            foreach ($columnOrder as $id) {
                if ($allColumnsKeyed->has($id)) {
                    $orderedColumns[] = $allColumnsKeyed->get($id);
                    $allColumnsKeyed->forget($id);
                }
            }
            foreach ($allColumnsKeyed as $col) {
                $orderedColumns[] = $col;
            }
            $allColumns = $orderedColumns;
        }

        $tableColumns = $allColumns;

        return view('admin.events.index', compact('events', 'allEvents', 'activePeriod', 'categories', 'members', 'customColumns', 'tableColumns')); 
    }
    public function create(Request $request) { 
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        $members = $activePeriod ? Member::with('division')->where('period_id', $activePeriod->id)->get() : collect();
        $categories = Category::all();
        $selectedDate = $request->query('date', date('Y-m-d'));
        return view('admin.events.create', compact('members', 'categories', 'selectedDate')); 
    }
    public function store(Request $request) {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Tidak dapat menambahkan kegiatan: belum ada periode aktif.');
        }

        $validated = $request->validate([
            'title' => 'required', 'description' => 'nullable|string', 'event_date' => 'required|date', 'event_time' => 'nullable|date_format:H:i', 'link' => 'nullable|url',
            'status' => 'required|in:ide,acc,selesai,ditunda,terjadwal,ditolak',
            'links' => 'nullable|array',
            'links.*.title' => 'nullable|string',
            'links.*.url' => 'nullable|url'
        ]);
        
        $cleanLinks = [];
        if ($request->has('links') && is_array($request->links)) {
            foreach ($request->links as $l) {
                if (!empty($l['url'])) {
                    $cleanLinks[] = [
                        'title' => !empty($l['title']) ? $l['title'] : 'Tautan',
                        'url' => $l['url']
                    ];
                }
            }
        }
        $validated['links'] = $cleanLinks;
        if (empty($validated['link']) && !empty($cleanLinks)) {
            $validated['link'] = $cleanLinks[0]['url'];
        }

        $validated['period_id'] = $activePeriod->id;
        
        $event = Event::create($validated);
        if ($request->has('members')) {
            $syncData = [];
            foreach ($request->members as $memberId) {
                $member = Member::find($memberId);
                if ($member) {
                    $role = $request->member_roles[$memberId] ?? null;
                    if ($role === 'Lainnya' && !empty($request->member_custom_roles[$memberId])) {
                        $role = trim($request->member_custom_roles[$memberId]);
                    }
                    $syncData[$memberId] = [
                        'division_id' => $member->division_id,
                        'role' => $role
                    ];
                }
            }
            $event->members()->sync($syncData);
        }
        if ($request->has('categories')) {
            $event->categories()->sync($request->categories);
        }
        return redirect()->route('admin.events.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }
    public function edit(Event $event) { 
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        $members = $activePeriod ? Member::with('division')->where('period_id', $activePeriod->id)->get() : collect();
        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'members', 'categories')); 
    }
    public function update(Request $request, Event $event) {
        $validated = $request->validate([
            'title' => 'required', 'description' => 'nullable|string', 'event_date' => 'required|date', 'event_time' => 'nullable', 'link' => 'nullable|url',
            'status' => 'required|in:ide,acc,selesai,ditunda,terjadwal',
            'links' => 'nullable|array',
            'links.*.title' => 'nullable|string',
            'links.*.url' => 'nullable|url'
        ]);

        $cleanLinks = [];
        if ($request->has('links') && is_array($request->links)) {
            foreach ($request->links as $l) {
                if (!empty($l['url'])) {
                    $cleanLinks[] = [
                        'title' => !empty($l['title']) ? $l['title'] : 'Tautan',
                        'url' => $l['url']
                    ];
                }
            }
        }
        $validated['links'] = $cleanLinks;
        if (empty($validated['link']) && !empty($cleanLinks)) {
            $validated['link'] = $cleanLinks[0]['url'];
        }

        $event->update($validated);
        if ($request->has('members')) {
            $syncData = [];
            foreach ($request->members as $memberId) {
                $member = Member::find($memberId);
                if ($member) {
                    $role = $request->member_roles[$memberId] ?? null;
                    if ($role === 'Lainnya' && !empty($request->member_custom_roles[$memberId])) {
                        $role = trim($request->member_custom_roles[$memberId]);
                    }
                    $syncData[$memberId] = [
                        'division_id' => $member->division_id,
                        'role' => $role
                    ];
                }
            }
            $event->members()->sync($syncData);
        } else {
            $event->members()->detach();
        }
        if ($request->has('categories')) {
            $event->categories()->sync($request->categories);
        } else {
            $event->categories()->detach();
        }
        return redirect()->route('admin.events.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }
    public function destroy(Event $event) {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function export() {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Belum ada periode aktif untuk ekspor.');
        }

        $events = Event::with(['categories', 'members.division'])
            ->where('period_id', $activePeriod ? $activePeriod->id : 0)
            ->orderBy('event_date', 'asc')
            ->get();
        
        $customColumns = \App\Models\Setting::get('event_custom_columns', []);
        if (is_string($customColumns)) {
            $customColumns = json_decode($customColumns, true) ?: [];
        }

        return response(view('admin.events.excel', [
            'events' => $events,
            'activePeriod' => $activePeriod,
            'customColumns' => $customColumns
        ]))
        ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
        ->header('Content-Disposition', 'attachment; filename="Laporan_Tabel_Kegiatan_' . date('Ymd_His') . '.xls"')
        ->header('Cache-Control', 'max-age=0');
    }

    public function move(Request $request, Event $event) {
        $request->validate([
            'event_date' => 'required|date',
            'event_time' => 'nullable'
        ]);

        $event->event_date = $request->event_date;
        if ($request->has('event_time')) {
            $event->event_time = $request->event_time;
        }
        $event->save();
        $event->load(['categories', 'members.division']);
        $divisions = \App\Models\Division::all()->keyBy('id');
        $event->members->transform(function($m) use ($divisions) {
            $pivotDivisionId = $m->pivot->division_id ?? $m->division_id;
            $m->pivot_division = $divisions->get($pivotDivisionId);
            $colors = ['Inti' => '#8b5cf6', 'Creative' => '#ec4899', 'Programmer' => '#3b82f6', 'Media' => '#f59e0b'];
            $divName = $m->pivot_division ? $m->pivot_division->name : '';
            $m->color = $colors[$divName] ?? '#64748b';
            return $m;
        });
        $event->formatted_time = $event->event_time ? $event->event_time->format('H:i') : null;

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil dipindahkan ke ' . \Carbon\Carbon::parse($event->event_date)->format('d M Y'),
            'event' => $event
        ]);
    }

    public function quickStore(Request $request) {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return response()->json(['success' => false, 'message' => 'Belum ada periode yang aktif.'], 422);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable',
            'status' => 'nullable|string|in:ide,acc,selesai,ditunda,terjadwal,ditolak',
            'category_id' => 'nullable',
            'member_id' => 'nullable',
            'role' => 'nullable|string',
            'custom_fields' => 'nullable|array'
        ]);

        $createData = [
            'period_id' => $activePeriod->id,
            'title' => $validated['title'],
            'event_date' => !empty($validated['event_date']) ? $validated['event_date'] : date('Y-m-d'),
            'event_time' => !empty($validated['event_time']) ? $validated['event_time'] : null,
            'status' => !empty($validated['status']) ? $validated['status'] : 'terjadwal',
            'custom_fields' => $validated['custom_fields'] ?? []
        ];

        $event = Event::create($createData);

        if (!empty($request->category_id)) {
            if (is_array($request->category_id)) {
                $event->categories()->sync($request->category_id);
            } else {
                $event->categories()->sync([$request->category_id]);
            }
        }
        if (!empty($request->member_id)) {
            if (is_array($request->member_id)) {
                $syncData = [];
                foreach ($request->member_id as $mId) {
                    $member = Member::find($mId);
                    if ($member) {
                        $syncData[$mId] = [
                            'division_id' => $member->division_id,
                            'role' => $request->role ?? 'Petugas'
                        ];
                    }
                }
                $event->members()->sync($syncData);
            } else {
                $member = Member::find($request->member_id);
                if ($member) {
                    $event->members()->sync([
                        $request->member_id => [
                            'division_id' => $member->division_id,
                            'role' => $request->role ?? 'Petugas'
                        ]
                    ]);
                }
            }
        }

        $event->load(['categories', 'members.division']);
        $divisions = \App\Models\Division::all()->keyBy('id');
        $event->members->transform(function($m) use ($divisions) {
            $pivotDivisionId = $m->pivot->division_id ?? $m->division_id;
            $m->pivot_division = $divisions->get($pivotDivisionId);
            $colors = ['Inti' => '#8b5cf6', 'Creative' => '#ec4899', 'Programmer' => '#3b82f6', 'Media' => '#f59e0b'];
            $divName = $m->pivot_division ? $m->pivot_division->name : '';
            $m->color = $colors[$divName] ?? '#64748b';
            return $m;
        });
        $event->formatted_time = $event->event_time ? $event->event_time->format('H:i') : null;

        return response()->json([
            'success' => true,
            'message' => 'Baris kegiatan baru berhasil dibuat!',
            'event' => $event
        ]);
    }

    public function inlineUpdate(Request $request, Event $event) {
        $field = $request->field;
        $value = $request->value;

        if ($field === 'title') {
            $event->title = $value ?: 'Kegiatan Baru';
        } elseif ($field === 'event_date') {
            $event->event_date = $value ?: date('Y-m-d');
        } elseif ($field === 'event_time') {
            $event->event_time = $value ?: null;
        } elseif ($field === 'status') {
            if (in_array($value, ['ide', 'acc', 'terjadwal', 'ditunda', 'selesai', 'ditolak'])) {
                $event->status = $value;
            }
        } elseif ($field === 'description') {
            $event->description = $value;
        } elseif ($field === 'category_id') {
            if ($value) {
                if (is_array($value)) {
                    $event->categories()->sync($value);
                } else {
                    $event->categories()->sync([$value]);
                }
            } else {
                $event->categories()->sync([]);
            }
        } elseif ($field === 'member_id') {
            if ($value) {
                if (is_array($value)) {
                    $syncData = [];
                    foreach ($value as $mId) {
                        $member = Member::find($mId);
                        if ($member) {
                            $syncData[$mId] = [
                                'division_id' => $member->division_id,
                                'role' => $request->role ?? 'Petugas'
                            ];
                        }
                    }
                    $event->members()->sync($syncData);
                } else {
                    $member = Member::find($value);
                    if ($member) {
                        $event->members()->sync([
                            $value => [
                                'division_id' => $member->division_id,
                                'role' => $request->role ?? 'Petugas'
                            ]
                        ]);
                    }
                }
            } else {
                $event->members()->sync([]);
            }
        } elseif ($field === 'custom_field') {
            $colId = $request->column_id;
            $fields = $event->custom_fields ?? [];
            $fields[$colId] = $value;
            $event->custom_fields = $fields;
        }

        $event->save();
        $event->load(['categories', 'members.division']);
        $divisions = \App\Models\Division::all()->keyBy('id');
        $event->members->transform(function($m) use ($divisions) {
            $pivotDivisionId = $m->pivot->division_id ?? $m->division_id;
            $m->pivot_division = $divisions->get($pivotDivisionId);
            $colors = ['Inti' => '#8b5cf6', 'Creative' => '#ec4899', 'Programmer' => '#3b82f6', 'Media' => '#f59e0b'];
            $divName = $m->pivot_division ? $m->pivot_division->name : '';
            $m->color = $colors[$divName] ?? '#64748b';
            return $m;
        });
        $event->formatted_time = $event->event_time ? $event->event_time->format('H:i') : null;

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui!',
            'event' => $event
        ]);
    }

    public function addColumn(Request $request) {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'nullable|string|in:text,dropdown,checklist,date',
            'options' => 'nullable|string|max:500'
        ]);

        $columns = \App\Models\Setting::get('event_custom_columns', []);
        if (is_string($columns)) {
            $columns = json_decode($columns, true) ?? [];
        }

        $newCol = [
            'id' => 'col_' . time() . '_' . rand(10, 99),
            'name' => trim($request->name),
            'type' => $request->type ?? 'text',
            'options' => $request->type === 'dropdown' ? trim($request->options ?? '') : ''
        ];

        $columns[] = $newCol;
        \App\Models\Setting::set('event_custom_columns', json_encode($columns));

        return response()->json([
            'success' => true,
            'message' => 'Kolom baru "' . $newCol['name'] . '" berhasil ditambahkan ke spreadsheet!',
            'columns' => $columns,
            'new_column' => $newCol
        ]);
    }

    public function deleteColumn(Request $request, $columnId) {
        $columns = \App\Models\Setting::get('event_custom_columns', []);
        if (is_string($columns)) {
            $columns = json_decode($columns, true) ?? [];
        }

        $columns = array_values(array_filter($columns, function($col) use ($columnId) {
            return ($col['id'] ?? '') !== $columnId;
        }));

        \App\Models\Setting::set('event_custom_columns', json_encode($columns));

        // Also clean up event_table_columns_order
        $columnOrder = \App\Models\Setting::get('event_table_columns_order', []);
        if (is_string($columnOrder)) {
            $columnOrder = json_decode($columnOrder, true) ?? [];
        }
        if (is_array($columnOrder)) {
            $columnOrder = array_values(array_filter($columnOrder, function($id) use ($columnId) {
                return $id !== $columnId;
            }));
            \App\Models\Setting::set('event_table_columns_order', json_encode($columnOrder));
        }

        return response()->json([
            'success' => true,
            'message' => 'Kolom kustom berhasil dihapus!',
            'columns' => $columns
        ]);
    }

    public function updateColumnOrder(Request $request) {
        $request->validate([
            'columns' => 'required|array',
            'columns.*' => 'string'
        ]);

        \App\Models\Setting::set('event_table_columns_order', json_encode($request->columns));

        return response()->json([
            'success' => true,
            'message' => 'Urutan kolom berhasil disimpan!'
        ]);
    }
}
