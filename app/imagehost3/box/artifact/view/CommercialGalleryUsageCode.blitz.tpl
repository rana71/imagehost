<!-- Galerie komercyjne, np Allegro prosto z imgED.pl ! | BEGIN -->
<style>
    .imged-ff { clear: both; }
    .imged-gallery { 
        text-align: center;     
        font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; 
        font-size: 10px; 
    }
    .imged-gallery .imged-top-desc, .imged-gallery .imged-bottom-desc { margin: 10px; color: #a5a5a5; }
    .imged-gallery ul { 
        width: 95%; 
        margin: 0 auto; 
        padding: 10px; 
        list-style-type: none; 
        background-color: #fff; 
        border: 1px solid #cecece;  
        -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;
    }
    .imged-gallery ul li { display: inline-block; }
    .imged-gallery ul a { display: block; padding: 1px; background-color: #fff; -moz-box-shadow: rgba(0,0,0,0.1) 0 0 5px; -webkit-box-shadow: rgba(0,0,0,0.1) 0 0 5px; box-shadow: rgba(0,0,0,0.1) 0 0 5px; }
    .imged-gallery ul a:hover { background-color: #b60000; }
    .imged-gallery ul img { height: 100px; } 
    .imged-gallery .imged-bottom-desc a { text-decoration: none; color: #b60000; }
    .imged-gallery .imged-bottom-desc a:hover { text-decoration: underline; }
</style>
<div class='imged-ff'></div>
<div class='imged-gallery'>
    <p class='imged-top-desc'>Kliknij na zdjęcie aby powiększyć</p>
    <ul>
        {{BEGIN arrElements}}
        <li>
            <a href='{{$strGalleryUrl}}#{{$id}}' title='{{$title}}' target='_blank'>
                <img src='{{$image_url}}' alt='{{$title}}' />
            </a>
        </li>
        {{END}}
    </ul>
    <p class='imged-bottom-desc'>
        Galeria wygenerowana dzięki serwisowi 
        <a href='{{this::url("Homepage")}}' title='imgED - Jeden obraz wart jest więcej niż tysiąc słów!' target='_blank'>imgED.pl</a>
    </p>
</div>
<div class='imged-ff'></div>
<!-- Galerie komercyjne, np do Allegro - prosto z imgED.pl ! | END -->
