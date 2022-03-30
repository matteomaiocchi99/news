$(document).ready(function (){
    initSummernote();

    initSwitch();

    //$('.float').mask('#.##9,99', {reverse:true}); //mette virgola in automatico per i numeri decimali e il . per le migliaia
});

//configurazione SummerNote con immagine
function initSummernote(){

    $('.summernote').each(function() {
        var text = $(this);

        $(this).summernote({
            height: 300,
            minHeight: 300,
            maxHeight: null,
            focus: false,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para',['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr', 'video']],
                ['view', ['fullscreen', 'codeview']],
                ['help', ['help']]
            ],
            callbacks:{
                onImageUpload: function (files, editor, welEditable){
                    sendFile(files[0], text);
                }
            }
        });
    });
    $('.summernote-no-image').each(function() {
        var height = 300;
        if($(this).attr("summernote-height")) {
            height = $(this).attr("summernote-height");
        }

        $(this).summernote({
            height: height,
            minHeight: 300,
            maxHeight: null,
            focus: false,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para',['ul', 'ol', 'paragraph', 'height']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
            ]
        });

    });
}

//questa funzione serve per la checkbox dello status
function initSwitch() {
    $('.js-switch').each(function() {
        if($(this).data("switchery") == null){
            new Switchery($(this)[0], $(this).data());
        }
    })
}