<div class='container col-md-10 col-md-offset-1 user-artifacts-contents'>
    {{IF $arrArtifacts}}
        <div class="dropdown pull-right">
            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                Sortowanie: 
                {{IF $strCurrentSort == 'najpopularniejsze'}}Najpopularniejsze
                {{ELSEIF $strCurrentSort == 'alfabetyczne'}}Alfabetyczne
                {{ELSE}}Najnowsze{{END if-list}}
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li><a href="{{this::url('User::myUploads', 'najnowsze')}}">Najnowsze</a></li>
                <li><a href="{{this::url('User::myUploads', 'najpopularniejsze')}}">Najpopularniejsze</a></li>
                <li><a href="{{this::url('User::myUploads', 'alfabetyczne')}}">Alfabetyczne</a></li>
            </ul>
        </div>
        <table class="table table-hover row">
            <thead>
            <th  class='col-xs-3 text-center' colspan="2"></th>
            <th class='text-center col-xs-1'>Popularność</th>
            <th class='col-xs-1'>Dodany</th>
            <th class='col-xs-4'>Link</th>
            <th class='col-xs-1 text-center'>Opcje</th>
            </thead>
            <tbody>
                {{BEGIN arrArtifacts}}
                <tr data-artifact-id='{{$id}}'>
                    <td class='text-center'>
                        <img class='thumb' src="{{$thumb_url}}" alt="{{q($title)}}" />
                    </td>
                    <td>
                        <a href='{{this::url("Details", $slug, $id)}}' title='Zobacz wrzutkę'>
                            {{q($title)}}
                            <span class='glyphicon glyphicon-new-window'></span>
                        </a>
                    </td>
                    <td class="text-center">
                        {{$shows_count_real}}
                    </td>
                    <td>{{this::prettyDate($add_timestamp)}}</td>
                    <td>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='{{this::url("Details", $slug, $id)}}' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                        <div class="copy-info text-success text-right">Skopiowano!</div>
                    </td>
                    <td class='text-center'>
                        <div class='options-contents'>
                            <button type="button" class="remove-artifact btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>
                        </div>
                    </td>
                </tr>
                {{END}}
            </tbody>
        </table>

    {{ELSE}}
        <div class="alert alert-warning" role="alert">Nie dodałeś jeszcze żadnego obrazka</div>
    {{END if-list}}
</div>
