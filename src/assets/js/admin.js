$(document).ready(function() {
    const ajaxForms = $('form.ajaxForm');
    $.each(ajaxForms, function(i, form) {
        $(form).on('submit', function(e) {
            e.preventDefault();
            ajaxSend(form)
        })
    })

    function ajaxSend(form) {
        let formData = new FormData(form);
        if (!form.action) {
            alert('Не указан url формы')
            return
        }
        console.log(formData)
        $.ajax({
            url: form.action,
            method: form.method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                alert(data)
            },
            error: function() {
                alert('Ошибка ajax запроса')
            }
        })
    }

})