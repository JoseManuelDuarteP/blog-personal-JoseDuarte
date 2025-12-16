//alert("imgAsc.js cargado");

$(document).on('click', '.tab-btn', function () {

    $('.tab-btn').removeClass('active');
    $(this).addClass('active');

    const categoria = $(this).data('category');

    $.ajax({
        url: '/imagenes/' + categoria,
        method: 'GET',
        success: function (data) {

            const grid = $('#collectionsGrid');
            grid.empty();

            if (data.length === 0) {
                grid.append('<p>No se encontraron imágenes.</p>');
                return;
            }

            data.forEach(function (image) {

                grid.append(`
                    <div class="collection-card">
                        <div class="collection-thumbnail">
                            <img src="/images/index/gallery/${image.file}" alt="${image.title}">
                        </div>
                        <div class="card-content">
                            <span class="card-badge">${image.category}</span>
                            <h3 class="card-title">${image.title}</h3>
                            <p class="card-subtitle">Views: ${image.views}</p>
                            <p class="card-subtitle">Likes: ${image.likes}</p>
                            <p class="card-price">$${image.price}</p>
                        </div>
                    </div>
                `);
            });
        },
        error: function () {
            alert('Error al cargar las imágenes');
        }
    });
});
