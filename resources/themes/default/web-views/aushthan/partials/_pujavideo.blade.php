
<style>
    .resp-iframe {}

    .resp-iframe__container {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
    }

    .resp-iframe__embed {
        position: absolute;
        top: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
</style>
@if($anushthan->video_url != null)
    @php

        $videoId = '';
        if (
            preg_match(
                '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
                $anushthan->video_url,
                $matches,
            )
        ) {
            $videoId = $matches[1];
        }
    @endphp

    @if ($videoId)
        <div class="resp-iframe">
            <div class="resp-iframe__container">

                <iframe width="420" height="315"
                    src="https://www.youtube.com/embed/{{ $videoId }}?autohide=1&modestbranding=1&rel=0&showinfo=0&controls=1&theme=dark"
                    frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>

            </div>
        </div>
    @endif
@endif
