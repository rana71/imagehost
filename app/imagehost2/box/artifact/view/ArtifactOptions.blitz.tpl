<div class="dropdown">
    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
        Więcej opcji
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
        <li role="presentation"><a role="menuitem" class='embeed-item' tabindex="-1" href="javascript:void(0);"><span class='icon glyphicon glyphicon-paperclip'></span> Pobierz kod</a></li>
        <li role="presentation"><a role="menuitem" class='send-by-email' tabindex="-1" href="mailto:?subject=Super%20obrazek%21%20Zobacz%20-%20{{$arrArtifact.title}}&body=Znalaz%C5%82em%20%C5%9Bwietny%20obrazek%20na%20{{this::url('Homepage')}}%20%3AD%20Zobacz%3A%20{{this::url("Details", $arrArtifact.slug, $arrArtifact.id)}}"><span class='icon glyphicon glyphicon-envelope'></span> Wyślij e-mailem</a></li>
        <li role="presentation"><a role="menuitem" class='report-item' tabindex="-1" href="javascript:void(0);"><span class='icon glyphicon glyphicon-flag'></span> Zgłoś nadużycie</a></li>
    </ul>
</div>
        
        
<div id="modal-embeed" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Pobierz kod obrazka</h4>
            </div>
            <div class="modal-body">
                <ul>
                    <li>
                        <strong>Link do tej strony</strong>
                        <span class="embeed-info">Skopiowano!</span>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='{{ this::url('Details', $arrArtifact.slug, $arrArtifact.id) }}' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                    </li>
                    <li>
                        <strong>Link bezpośredni do obrazka</strong>
                        <span class="embeed-info">Skopiowano!</span>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='{{$arrArtifact.thumb_url}}' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                    </li>
                    <li>
                        <strong>Umieść na swojej stronie obrazek</strong>
                        <span class="embeed-info">Skopiowano!</span>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='<img src="{{$arrArtifact.thumb_url}}" alt="{{$arrArtifact.title}}" />' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                    </li>
                    <li>
                        <strong>Obrazek w BBCode</strong>
                        <span class="embeed-info">Skopiowano!</span>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='[img]{{$arrArtifact.thumb_url}}[img]' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                    </li>
                    <li>
                        <strong>Obrazek z linkiem w BBCode</strong>
                        <span class="embeed-info">Skopiowano!</span>
                        <div class="input-group">
                            <input type='text' readonly='readonly' class='form-control' value='[url={{ this::url('Details', $arrArtifact.slug, $arrArtifact.id) }}][img]{{$arrArtifact.thumb_url}}[/img][/url]' onfocus='javascript:this.select();' />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary go-copy"> 
                                    <i class="glyphicon glyphicon-link"></i>
                                </button> 
                            </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div id="modal-report" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Zgłoś nadużycie</h4>
            </div>
            <form class="form-horizontal">
                <div class="modal-body">

                    <div class="form-group required">
                        <label for="input-name" class="col-sm-4 control-label">Twoje imię</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" required="required" value='' id="input-name" placeholder="" />
                        </div>
                    </div>
                    <div class="form-group required">
                        <label for="input-email" class="col-sm-4 control-label">Twój adres e-mail</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" required="required" value='' id="input-email" placeholder="" />
                        </div>
                    </div>
                    <div class="form-group required">
                        <label for="input-url" class="col-sm-4 control-label">Adres URL obrazka</label>
                        <div class="col-sm-8">
                            <input type="url" class="form-control" required="required" readonly='readonly' value='{{this::url("Details", $arrArtifact.slug, $arrArtifact.id)}}' id="input-url" placeholder="" />
                        </div>
                    </div>
                    <div class="form-group required">
                        <label for="input-reason" class="col-sm-4 control-label">Wyjaśnienie zgłoszenia</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" required="required" id='input-reason' rows="3"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-link" data-dismiss="modal">Zrezygnuj</button>
                    <button type="submit" class="btn btn-primary">Wyślij zgłoszenie</button>
                </div>
            </form>
        </div>
    </div>
    <input type='hidden' readonly='readonly' id='artifact-id' value='{{$arrArtifact.id}}' />
</div>