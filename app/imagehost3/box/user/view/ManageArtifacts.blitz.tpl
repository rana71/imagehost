<div class='m-user'>
    <h1>Twoje obrazki</h1>
    
    <div class="main-content user-artifacts">
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
            <table>
                <thead>
                    <tr>
                        <th class='col1'>&nbsp;</th>
                        <th class='col2'>
                            Suma odsłon: {{$numTotalShows}} <span class="show-stats" data-type="general"><i class='glyphicon glyphicon-signal'></i></span></span>
                        </th>
                        <th class='col3'>Dodany</th>
                        <th class='col4'>Link</th>
                        <th class='col5'>Opcje</th>
                    </tr>
                    <tr >
                        <th colspan='5' style='border-top:0;'>
                            <div class='stats-container' id='stats-chart-general'></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {{BEGIN arrArtifacts}}
                        <tr data-artifact-id='{{$id}}'>
                            <td class="art-thumb col1">
                                <img src="{{$thumb_url}}" alt="{{q($title)}}" />
                            </td>
                            <td class="art-title col2">
                                <a href='{{this::url("Details", $slug, $id)}}' title='Zobacz'>{{q($title)}}</a><br />
                                {{IF $boolOnlyImages == false}}
                                     <small class='tip'>Aby wygenerować galerię Allegro galeria musi składać się z samych zdjęć</small><br />
                                {{END if-list}}
                                Odsłony: {{$shows_count_real}} 
                                <span class="show-stats" data-type="item" data-item-id="{{$id}}"><i class='glyphicon glyphicon-signal'></i></span>
                            </td>
                            <td class="add col3">{{this::prettyDate($add_timestamp)}}</td>
                            <td class="link col4">
                                <input type='text' readonly='readonly' class='form-control' value='{{this::url("Details", $slug, $id)}}' onfocus="javascript:this.select();" />
                            </td>
                            <td class="opts col5">
                                 <div class='options-contents'>
                                     {{IF $boolOnlyImages == true}}
                                        <button type="button" class="allegro-gallery"></button>
                                     {{END if-list}}
                                     <button type="button" class="remove">Usuń</button>
                                 </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan='5'>
                                <div class='stats-container' id='stats-chart-artifact-{{$id}}'></div>
                            </td>
                        </tr>
                    {{END}}
                </tbody>
            </table>
                            
            <div id="stats-modal" class="modal fade">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Statystyki odsłon</h4>
                        </div>
                        <div class="modal-body">
                            <div id='modal-stats-wrapper' style='width:100%;'></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id='stats-wrapper'></div>
            
            <div id="commercial-gallery-modal" class="modal fade">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Galeria Allegro</h4>
                        </div>
                        <div class="modal-body">
                            <p>Aby dodać galerię do Allegro skopiuj poniższy kod i wklej go w pole edytora <strong>tekstowego</strong> opisu aukcji Allegro podczas wystawiania!</p>
                            <form method="post">
                                <div class='r'>
                                    <textarea rows='4'></textarea>
                                    <small class='tip'>Naciśnij ctrl+c aby skopiować kod do schowka</small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                                
        {{ELSE}}
            <div class="alert alert-warning" role="alert">Nie dodałeś jeszcze żadnego obrazka</div>
        {{END if-list}}
    </div>
</div>