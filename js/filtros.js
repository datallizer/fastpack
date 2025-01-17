$(document).ready(function(){

    // Función para filtrar productos
    function filterProducts(){
        var selectedCategories = [];
        var selectedIndustries = [];

        // Recolectar las categorías seleccionadas
        $('.category_item:checked').each(function(){
            selectedCategories.push($(this).val());
        });

        // Recolectar las industrias seleccionadas
        $('.industry_item:checked').each(function(){
            selectedIndustries.push($(this).val());
        });

        // Si no hay categorías o industrias seleccionadas, mostrar todos los productos
        if((selectedCategories.length === 0 || selectedCategories.includes('all')) && 
           (selectedIndustries.length === 0 || selectedIndustries.includes('all'))) {
            $('.product-item').show().css('transform', 'scale(1)');
        } else {
            // Ocultar todos los productos primero
            $('.product-item').hide().css('transform', 'scale(0)');
            
            // Mostrar productos que coincidan con las categorías e industrias seleccionadas
            $('.product-item').each(function(){
                var productCategories = $(this).attr('category').split(', ');
                var productIndustries = $(this).attr('industry').split(', ');

                var matchCategory = selectedCategories.length === 0 || selectedCategories.includes('all') || productCategories.some(category => selectedCategories.includes(category));
                var matchIndustry = selectedIndustries.length === 0 || selectedIndustries.includes('all') || productIndustries.some(industry => selectedIndustries.includes(industry));

                if(matchCategory && matchIndustry) {
                    $(this).show().css('transform', 'scale(1)');
                }
            });
        }
    }

    // Evento cuando un checkbox de categoría cambia de estado
    $('.category_item').on('change', function(){
        // Si "Todo" es seleccionado, desmarcar otros checkboxes de categoría e industria
        if($(this).val() === 'all' && $(this).is(':checked')){
            $('.category_item, .industry_item').prop('checked', false);
            $(this).prop('checked', true);
        } else {
            $('.category_item[value="all"]').prop('checked', false);
        }
        filterProducts();
    });

    // Evento cuando un checkbox de industria cambia de estado
    $('.industry_item').on('change', function(){
        // Desmarcar "Todo" si se selecciona alguna industria
        $('.category_item[value="all"]').prop('checked', false);
        filterProducts();
    });

});
