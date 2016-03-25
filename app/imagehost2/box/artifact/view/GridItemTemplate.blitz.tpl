<div class='item{{IF $is_vertical}} vertical{{end if-list}}'>
    <a href='{{this::url("Details", $slug, $id)}}' title='{{q($title)}}'>
        <img src='{{$thumb_url}}' class='thumb' alt="{{q($title)}}" />
        <div class='layer {{if $description}}collapseable{{end if-list}}'>
            <div class="icons">
                <span class='glyphicon glyphicon-eye-open'></span> {{$shows_count}}
            </div>
            <div class='title'>{{plaintext($title)}}</div>
            {{if $description}}
                <div class='desc'>{{plaintext($description)}}</div>
            {{end if-list}}
        </div>
    </a>
</div>
 