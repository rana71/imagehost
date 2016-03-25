<li>
    <figure>
        <iframe width="728" class='yt-video' height="410" src="https://www.youtube.com/embed/{{$arrElement.youtube_id}}" frameborder="0" allowfullscreen></iframe>
        
        <div class="info">
            <div class="title">
                <h2>{{q($arrElement.title)}}</h2>
                <div class="options">Opcje</div>
            </div>
                {{IF $arrElement.description}}
                <div class="desc">
                    <figcaption>{{this::html($arrElement.description)}}</figcaption>
                </div>
            {{END if-list}}
          
        </div>
    </figure>
</li>