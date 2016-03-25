function ErrorizeForm(objErrors, elForm) {
    var strFieldName = '';
    var elField = null;
    
    for (strFieldName in objErrors) {
        elField = elForm.find('[name="'+strFieldName+'"]');
        elField.closest('.form-group').addClass('validate-has-error');
        elField.after('<span class="validate-has-error">'+objErrors[strFieldName]+'</span>');
    }
};

function UnerrorizeForm(elForm) {
    
    elForm.find('span.validate-has-error').remove();
    elForm.find('.form-group.validate-has-error').removeClass('validate-has-error');
    
};