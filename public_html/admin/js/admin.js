//?var imagehost2 = {};
//imagehost2.Box = {};
Subframe.Apps.admin = function (arrCurrentBoxes) {

    "use strict";
    var $this = this;
    $this.strName = 'admin';
    this.objBoxes = {};
    this.arrCurrentBoxes = arrCurrentBoxes;
    this.objBoxController = {};
    this.arrBootstrapFiles = [
        '/subframe/js/Global.js',
        '/subframe/js/JsonRpc2.js',
        '/subframe/js/Loader.js',
        '/subframe/js/BoxController.js',
        'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js', 
        '/admin/assets/js/gsap/main-gsap.js', 
        '/admin/assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js', 
        '/admin/assets/js/bootstrap.js', 
        '/admin/assets/js/joinable.js', 
        '/admin/assets/js/neon-api.js', 
        '/admin/assets/js/resizeable.js', 
        '/admin/assets/js/toastr.js', 
        '/admin/assets/js/jquery.validate.min.js',
        '/admin/js/global.js', 
        '/admin/assets/js/neon-custom.js'
    ];
    this.objLoader = {};

    this.init = function () {};

    this.loaded = function () {
        $(function () {
            $this.objBoxController = new Subframe.Lib.BoxController($this.strName);
            $this.objBoxController.init($this.arrCurrentBoxes);
        });
    };

};