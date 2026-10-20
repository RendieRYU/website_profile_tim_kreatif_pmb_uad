<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    .table-header {
        background-color: #f1f5f9;
        font-weight: bold;
        text-align: center;
        border: 1px solid #cbd5e1;
    }
    .data-cell {
        border: 1px solid #e2e8f0;
    }
    .text-center {
        text-align: center;
    }
    .text-left {
        text-align: left;
    }
    .font-bold {
        font-weight: bold;
    }
</style>
</head>
<body>
<table>
    <thead>
        <tr>
            <th class="table-header">No</th>
            <th class="table-header">Judul Kegiatan</th>
            <th class="table-header">Deskripsi</th>
            <th class="table-header">Tanggal</th>
            <th class="table-header">Waktu</th>
            <th class="table-header">Status</th>
            <th class="table-header">Link Dokumentasi</th>
            <th class="table-header">Kategori</th>
            <th class="table-header">Anggota Terlibat</th>
            @foreach($customColumns ?? [] as $col)
            <th class="table-header">{{ $col['name'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($events as $index => $event)
        @php
            $categoriesStr = $event->categories->pluck('name')->join(', ');
            $membersStr = $event->members->map(function($m) {
                return $m->full_name . ' (' . ($m->pivot->role ?? '-') . ')';
            })->join(', ');
        @endphp
        <tr>
            <td class="data-cell text-center">{{ $index + 1 }}</td>
            <td class="data-cell text-left font-bold">{{ $event->title }}</td>
            <td class="data-cell text-left">{{ $event->description ?? '-' }}</td>
            <td class="data-cell text-center">{{ $event->event_date ? $event->event_date->format('Y-m-d') : '-' }}</td>
            <td class="data-cell text-center">{{ $event->event_time ? $event->event_time->format('H:i') : '-' }}</td>
            <td class="data-cell text-center font-bold">{{ strtoupper($event->status) }}</td>
            <td class="data-cell text-left">{{ $event->link ?? '-' }}</td>
            <td class="data-cell text-left">{{ $categoriesStr ?: '-' }}</td>
            <td class="data-cell text-left">{{ $membersStr ?: '-' }}</td>
            @foreach($customColumns ?? [] as $col)
            <td class="data-cell text-left">
                @if(isset($event->custom_fields[$col['id']]))
                    @if(($col['type'] ?? 'text') === 'checklist')
                        {{ $event->custom_fields[$col['id']] ? 'Ya' : 'Tidak' }}
                    @else
                        {{ $event->custom_fields[$col['id']] }}
                    @endif
                @else
                    -
                @endif
            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
