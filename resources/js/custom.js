$(document).ready(function() {
    $('.verification-code').on('input', function(e) {
        // Convertir a mayúsculas
        this.value = this.value.toUpperCase();

        // Mover al siguiente campo si se alcanza la longitud máxima
        if (this.value.length == this.maxLength) {
            $(this).next('.verification-code').focus();
        }

        // Actualizar el valor del código de verificación completo
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
    }).on('paste', function(e) {
        e.preventDefault();
        let pasteData = (e.originalEvent || e).clipboardData.getData('text/plain').toUpperCase();
        let fields = $('.verification-code');
        for (let i = 0; i < fields.length; i++) {
            fields[i].value = pasteData[i] || '';
        }
        fields.eq(pasteData.length).focus();

        // Actualizar el valor del código de verificación completo
        let verificationCode = '';
        $('.verification-code').each(function() {
            verificationCode += $(this).val();
        });
        $('#verification_code').val(verificationCode);
    });
});