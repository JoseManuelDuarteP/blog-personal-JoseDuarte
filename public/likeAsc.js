$(document).ready(function() {
    $('.like').on('click', function (e) {
        e.preventDefault(); // Previene cualquier comportamiento por defecto

        // Obtenemos el ID desde el atributo data-id del botón
        let imageId = $(this).data('id');
        let $boton = $(this); // Guardamos referencia al botón por si queremos cambiarle estilo

        $.ajax({
            url: '/like/' + imageId,
            method: 'POST',
            success: function (response) {
                // Actualizamos solo el número dentro del span
                $('#likes-counter').text(response.numLikes);
                
                // Opcional: Feedback visual en el botón
                if(response.liked) {
                   $boton.text('Unlike'); // O cambiar clase CSS
                   $boton.css('background-color', 'red');
                } else {
                   $boton.text('Like');
                   $boton.css('background-color', ''); 
                }
            },
            error: function(xhr) {
                if(xhr.status === 401) {
                    alert("Debes iniciar sesión para dar like");
                    window.location.href = '/login'; // O tu ruta de login
                } else {
                    console.log('Error:', xhr);
                }
            }
        });
    });
});