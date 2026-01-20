$(document).ready(function() {
    $('.btn-like-post').on('click', function (e) {
        e.preventDefault();

        const $boton = $(this);
        const postId = $boton.data('id');

        $boton.prop('disabled', true); // Evitar doble click

        $.ajax({
            url: '/like_post/' + postId,
            method: 'POST',
            success: function (response) {
                // 1. Actualizar número
                $('#likes-counter').text('Likes: ' + response.numLikes);
                
                // 2. Actualizar estado del botón (Toggle Class)
                if(response.liked) {
                   $boton.addClass('liked');
                   $boton.text('Unlike');
                } else {
                   $boton.removeClass('liked');
                   $boton.text('Dar Like');
                }
            },
            error: function(xhr) {
                if(xhr.status === 401) {
                    alert("Debes iniciar sesión para dar like");
                } else {
                    console.error('Error:', xhr);
                }
            },
            complete: function() {
                $boton.prop('disabled', false);
            }
        });
    });
});