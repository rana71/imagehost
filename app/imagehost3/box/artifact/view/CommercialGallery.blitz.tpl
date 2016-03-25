<div class='commercial-gallery'>
    <div class='main'>
        <div class="arrow left">
            <span></span>
        </div>
        <div class="photo">
            <div class="loading">Trwa ładowanie galerii</div>
        </div>
        <div class="arrow right">
            <span></span>
        </div>
    </div>
    <div class='thumbs'>   
        <ul>
            {{BEGIN arrElements}}
                <li data-item-element-id="{{$id}}">
                    <a href='{{this::currentUrl()}}#{{$id}}' title='Zobacz zdjęcie {{$title}}'>
                        <img src="{{$thumb_url}}" alt="{{$title}}" />
                    </a>
                </li>
            {{END}}
        </ul>
    </div>
    <div class='imged-link'>
        <a href="{{this::url('Details', $arrArtifact.slug, $arrArtifact.id)}}" title="{{ $arrArtifact.title }}">Zobacz tą galerię na imgED</a>
    </div>
</div> 