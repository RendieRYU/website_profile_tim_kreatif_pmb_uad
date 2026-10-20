<table>
    <thead>
        <tr>
            <th style="font-weight: bold; background-color: #f1f5f9;">No</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Tanggal</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Jenis Konten</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Pillar</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Link Konten</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Views / Reach</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Impression FYP</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Impression Profile</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Impression Search</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Impression Hashtag</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Impression Other</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Followers (%)</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Non-Followers (%)</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Likes</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Comments</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Shares</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Saves</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">Reposts</th>
            <th style="font-weight: bold; background-color: #e2e8f0;">Total Engagement</th>
            <th style="font-weight: bold; background-color: #f1f5f9;">New Followers</th>
            <th style="font-weight: bold; background-color: #dbeafe;">ER by Post (%)</th>
            <th style="font-weight: bold; background-color: #f3e8ff;">ER by Reach (%)</th>
            @foreach($customColumns ?? [] as $col)
            <th style="font-weight: bold; background-color: #e0e7ff;">{{ $col['name'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($posts as $index => $post)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $post->tanggal_posting->format('Y-m-d') }}</td>
            <td>{{ $post->jenis_konten }}</td>
            <td>{{ $post->pillar }}</td>
            <td>{{ $post->link_konten }}</td>
            <td>{{ $post->reach }}</td>
            <td>{{ $post->impression_home }}</td>
            <td>{{ $post->impression_profile }}</td>
            <td>{{ $post->impression_explore }}</td>
            <td>{{ $post->impression_hashtag }}</td>
            <td>{{ $post->impression_other }}</td>
            <td>{{ $post->interaction_followers }}%</td>
            <td>{{ $post->interaction_non_followers }}%</td>
            <td>{{ $post->likes }}</td>
            <td>{{ $post->comments }}</td>
            <td>{{ $post->shares }}</td>
            <td>{{ $post->saves }}</td>
            <td>{{ $post->reposts }}</td>
            <td style="font-weight: bold;">{{ $post->total_engagement }}</td>
            <td>{{ $post->new_followers }}</td>
            <td style="color: #2563eb; font-weight: bold;">{{ $post->er_by_post }}%</td>
            <td style="color: #9333ea; font-weight: bold;">{{ $post->er_by_reach }}%</td>
            @foreach($customColumns ?? [] as $col)
            <td>
                @if(isset($post->custom_fields[$col['id']]))
                    @if(($col['type'] ?? '') === 'checklist')
                        {{ $post->custom_fields[$col['id']] === 'ya' ? 'Ya' : 'Tidak' }}
                    @else
                        {{ $post->custom_fields[$col['id']] }}
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
