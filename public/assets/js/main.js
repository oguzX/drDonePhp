var selectedElement = null;

$(document).ready(function () {

    function swalMessage($title, $text, $type){
        Swal.fire({
            title: $title,
            text: $text,
            icon: $type,
            confirmButtonText: 'Tamam'
        })
    }

    $('.ajaxRequest').on('click', function () {
        $element = $(this);

        url = $element.data('href');
        data = $element.data('data');
        var callbackSuccess = $element.data('callback-success');
        selectedElement = $element;


        Swal.fire({
            title: "İşlem yapılıyor...",
            text: "Lütfen Bekleyiniz",
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $.ajax({
            data:data,
            url:url,
            success:function (response) {
                swalMessage('Başarılı!', response.message, 'success');
                eval(callbackSuccess);
            },
            error: function (response) {
                console.log(response);
                message = response.responseJSON.message;
                swalMessage('Hata', message,'error');
            }
        });
    });
    
    function toggleWishlist($status) {
        if($status=='added'){
            selectedElement.html('<i class="fa fa-minus-square"></i> İstek Listemden Çıkar</a>');
            selectedElement.data('callback-success',"toggleWishlist('removed')")
        }else{
            selectedElement.html('<i class="fa fa-plus-square"></i> İstek Listeme Ekle</a>');
            selectedElement.data('callback-success',"toggleWishlist('added')")
        }
    }

    function removeImage(selectorId) {
        $('#'+selectorId).remove();
    }
});