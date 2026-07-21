<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Portofolio - {{ $member->full_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1D4ED8;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #1D4ED8;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #555;
            font-size: 14px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #1D4ED8;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .item {
            margin-bottom: 10px;
        }
        .item-title {
            font-weight: bold;
        }
        .item-date {
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    @php
        $photoBase64 = null;
        if ($member->photo && file_exists(public_path('storage/' . $member->photo))) {
            $photoPath = public_path('storage/' . $member->photo);
            $type = strtolower(pathinfo($photoPath, PATHINFO_EXTENSION));
            // Dompdf requires the GD extension to render PNG images.
            // If GD is not loaded, we do not attempt to load/render PNG images to prevent crashes.
            if ($type !== 'png' || extension_loaded('gd')) {
                $data = @file_get_contents($photoPath);
                if ($data) {
                    $photoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }
        }
    @endphp

    <div class="header">
        @if($photoBase64)
            <img src="{{ $photoBase64 }}" alt="Foto" style="width: 120px; height: 120px; object-fit: cover; border-radius: 60px; margin-bottom: 15px;">
        @endif
        <h1>{{ $member->full_name }}</h1>
        <p>{{ $member->division->name ?? 'Tim Kreatif PMB UAD' }} {{ $member->role ? ' - '.$member->role : '' }}</p>
    </div>

    <div class="section">
        <h2>Riwayat Kegiatan</h2>
        @if($member->events->count() > 0)
            @foreach($member->events as $event)
                <div class="item">
                    <div class="item-title">{{ $event->title }}</div>
                    <div class="item-date">{{ $event->event_date->format('d F Y') }}</div>
                </div>
            @endforeach
        @else
            <p>Belum ada riwayat kegiatan.</p>
        @endif
    </div>

    <div class="section">
        <h2>Hasil Karya</h2>
        @if($member->works->count() > 0)
            @foreach($member->works as $work)
                <div class="item">
                    <div class="item-title">{{ $work->title }} ({{ $work->work_type }})</div>
                    <div class="item-date">{{ $work->work_date->format('d F Y') }}</div>
                    @if($work->link)
                        <div style="font-size: 12px; margin-top: 2px;"><a href="{{ $work->link }}">{{ $work->link }}</a></div>
                    @endif
                </div>
            @endforeach
        @else
            <p>Belum ada karya yang diunggah.</p>
        @endif
    </div>

</body>
</html>
