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
            $type = pathinfo($photoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($photoPath);
            $photoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
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
        <h2>Karya / Publikasi</h2>
        @if($member->news->count() > 0)
            @foreach($member->news as $news)
                <div class="item">
                    <div class="item-title">{{ $news->title }}</div>
                    <div class="item-date">{{ $news->published_date->format('d F Y') }}</div>
                </div>
            @endforeach
        @else
            <p>Belum ada karya yang dipublikasikan.</p>
        @endif
    </div>

</body>
</html>
