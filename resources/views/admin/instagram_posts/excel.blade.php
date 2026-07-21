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
            <th class="table-header">Tanggal Posting</th>
            <th class="table-header">Link Konten</th>
            <th class="table-header">Jenis Konten</th>
            <th class="table-header">Pillar</th>
            <th class="table-header">Reach</th>
            <th class="table-header">Impression Home</th>
            <th class="table-header">Impression Profile</th>
            <th class="table-header">Impression Explore</th>
            <th class="table-header">Impression Hashtag</th>
            <th class="table-header">Impression Other</th>
            <th class="table-header">Interaction Followers (%)</th>
            <th class="table-header">Interaction Non-Followers (%)</th>
            <th class="table-header">Likes</th>
            <th class="table-header">Comments</th>
            <th class="table-header">Shares</th>
            <th class="table-header">Saves</th>
            <th class="table-header">Reposts</th>
            <th class="table-header font-bold" style="background-color: #e2e8f0;">Total Engagement</th>
            <th class="table-header">New Followers</th>
            <th class="table-header" style="color: #2563eb;">ER by Post (%)</th>
            <th class="table-header" style="color: #7c3aed;">ER by Reach (%)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($posts as $index => $post)
        <tr>
            <td class="data-cell text-center">{{ $index + 1 }}</td>
            <td class="data-cell text-center">{{ $post->tanggal_posting->format('Y-m-d') }}</td>
            <td class="data-cell text-left">{{ $post->link_konten ?? '-' }}</td>
            <td class="data-cell text-center">{{ $post->jenis_konten }}</td>
            <td class="data-cell text-left">{{ $post->pillar ?? '-' }}</td>
            <td class="data-cell text-center">{{ $post->reach }}</td>
            <td class="data-cell text-center">{{ $post->impression_home }}</td>
            <td class="data-cell text-center">{{ $post->impression_profile }}</td>
            <td class="data-cell text-center">{{ $post->impression_explore }}</td>
            <td class="data-cell text-center">{{ $post->impression_hashtag }}</td>
            <td class="data-cell text-center">{{ $post->impression_other }}</td>
            <td class="data-cell text-center">{{ $post->interaction_followers }}%</td>
            <td class="data-cell text-center">{{ $post->interaction_non_followers }}%</td>
            <td class="data-cell text-center">{{ $post->likes }}</td>
            <td class="data-cell text-center">{{ $post->comments }}</td>
            <td class="data-cell text-center">{{ $post->shares }}</td>
            <td class="data-cell text-center">{{ $post->saves }}</td>
            <td class="data-cell text-center">{{ $post->reposts }}</td>
            <td class="data-cell text-center font-bold" style="background-color: #f8fafc;">{{ $post->total_engagement }}</td>
            <td class="data-cell text-center">{{ $post->new_followers }}</td>
            <td class="data-cell text-center font-bold" style="color: #2563eb;">{{ $post->er_by_post }}%</td>
            <td class="data-cell text-center font-bold" style="color: #7c3aed;">{{ $post->er_by_reach }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
