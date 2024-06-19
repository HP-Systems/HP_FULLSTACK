$(document).ready(function() {
    $('.verification-code').keyup(function() {
        if (this.value.length == this.maxLength) {
            $(this).next('.verification-code').focus();
        }
        
        let verificationCode = '';
        $('.verification-code').each(function() {
            verificationCode += $(this).val();
        });
        $('#verification_code').val(verificationCode);
    });

    
});


