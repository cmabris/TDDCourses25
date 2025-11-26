<div>
    <iframe src="https://player.vimeo.com/video/{{ $video->vimeo_id }}"
            webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
    <h3>{{ $video->title }} ({{ $video->getReadableDuration() }}min)</h3>
    <p>{{ $video->description }}</p>
    <ul>
        @foreach($courseVideos as $courseVideo)
            <li>
                <a href="{{ route('pages.course-videos', $courseVideo) }}">
                    {{ $courseVideo->title }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
