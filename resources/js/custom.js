$(document).ready(function() {
    $('.verification-code').keyup(function(e) {
        if (this.value.length == this.maxLength) {
            $(this).next('.verification-code').focus();
        }
        
        let verificationCode = '';
        $('.verification-code').each(function() {
            verificationCode += $(this).val();
        });
        $('#verification_code').val(verificationCode);
    }).keydown(function(e) {
        // Verificar si se presiona la tecla de retroceso
        if (e.key === "Backspace") {
            if (this.value.length === 0 || (this.selectionStart === 0 && this.selectionEnd === 0)) {
                $(this).prev('.verification-code').focus();
                
                let prevValue = $(this).prev('.verification-code').val();
                if (prevValue.length > 0) {
                    $(this).prev('.verification-code').val(prevValue.substring(0, prevValue.length - 1));
                }
            }
        }
    });
});