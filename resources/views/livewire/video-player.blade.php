<div>
    <iframe src="https://player.vimeo.com/video/{{ $video->vimeo_id }}"
            webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
    <h3>{{ $video->title }} ({{ $video->getReadableDuration() }}min)</h3>
    <p>{{ $video->description }}</p>
</div>
