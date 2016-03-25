{{BEGIN arrLeft }}
<a href="{{this::url('Details', slug, id)}}" title='{{title}} (poprzedni)' class='nav-horizontal left'><span>{{title}}</span><i></i></a>
{{END}}

{{BEGIN arrRight }}
    <a href="{{this::url('Details', slug, id)}}" title='{{title}} (następny}' class='nav-horizontal right'><i></i><span>{{title}}</span></a>
{{END}}