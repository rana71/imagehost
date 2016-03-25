<div class='col-md-12 text-center'>
    {{BEGIN arrTags}}
        <a href='{{this::url('Listing::tag', $slug)}}' title='Tag {{q($title)}}' class='tag badge'>{{$title}}</a>
    {{END}}
</div>