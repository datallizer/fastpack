$(document).ready(function () {

    //-------------------------------------------------------------
    // 1. AUTO-SELECT basándose en los parámetros de la URL
    //-------------------------------------------------------------
    const params = new URLSearchParams(window.location.search);
    const urlCategory = params.get("category");
    const urlSubcategory = params.get("subcategory");
    const urlIndustry = params.get("industry");

    // Función para convertir texto a slug sin eliminar acentos
    function toSlug(text) {
        return text
            .toLowerCase()
            .replace(/\s+/g, "-"); // espacios → guiones
    }

    // Autoselect categorías
    if (urlCategory) {
        $(".category_item").each(function () {
            const value = $(this).val();
            if (toSlug(value) === urlCategory) {
                $(this).prop("checked", true);
            }
        });
    }

    // Autoselect subcategorías
    if (urlSubcategory) {
        $(".subcategory_item").each(function () {
            const value = $(this).val();
            if (toSlug(value) === urlSubcategory) {
                $(this).prop("checked", true);
            }
        });
    }

    // Autoselect industrias
    if (urlIndustry) {
        $(".industry_item").each(function () {
            const value = $(this).val();
            if (toSlug(value) === urlIndustry) {
                $(this).prop("checked", true);
            }
        });
    }

    //-------------------------------------------------------------
    // 2. FUNCIÓN DE FILTRADO YA EXISTENTE
    //-------------------------------------------------------------
    function filterProducts() {
        var selectedCategories = [];
        var selectedSubcategories = [];
        var selectedIndustries = [];

        // Recolectar categorías seleccionadas
        $('.category_item:checked').each(function () {
            selectedCategories.push($(this).val());
        });

        // Recolectar subcategorías seleccionadas
        $('.subcategory_item:checked').each(function () {
            selectedSubcategories.push($(this).val());
        });

        // Recolectar industrias seleccionadas
        $('.industry_item:checked').each(function () {
            selectedIndustries.push($(this).val());
        });

        // Si se selecciona "Todo" o no hay filtros seleccionados, mostrar todo
        if ($('.all_item').is(':checked') ||
            (selectedCategories.length === 0 &&
             selectedSubcategories.length === 0 &&
             selectedIndustries.length === 0)) {

            $('.product-item').show().css('transform', 'scale(1)');
            return;
        }

        // Ocultar todo
        $('.product-item').hide().css('transform', 'scale(0)');

        // Mostrar los productos que cumplen los filtros
        $('.product-item').each(function () {

            var productCategories = ($(this).attr('category') || "").split(', ');
            var productSubcategories = ($(this).attr('subcategory') || "").split(', ');
            var productIndustries = ($(this).attr('industry') || "").split(', ');

            var matchCategory =
                selectedCategories.length === 0 ||
                productCategories.some(cat => selectedCategories.includes(cat));

            var matchSubcategory =
                selectedSubcategories.length === 0 ||
                productSubcategories.some(sub => selectedSubcategories.includes(sub));

            var matchIndustry =
                selectedIndustries.length === 0 ||
                productIndustries.some(ind => selectedIndustries.includes(ind));

            if (matchCategory && matchSubcategory && matchIndustry) {
                $(this).show().css('transform', 'scale(1)');
            }
        });
    }

    //-------------------------------------------------------------
    // 3. EVENTOS DE CHECKBOXES
    //-------------------------------------------------------------

    // Checkbox "Todo"
    $('.all_item').on('change', function () {
        if ($(this).is(':checked')) {
            $('.category_item, .subcategory_item, .industry_item').prop('checked', false);
        }
        filterProducts();
    });

    // Categorías
    $('.category_item').on('change', function () {
        if ($(this).is(':checked')) {
            $('.all_item').prop('checked', false);
        }
        filterProducts();
    });

    // Subcategorías
    $('.subcategory_item').on('change', function () {
        if ($(this).is(':checked')) {
            $('.all_item').prop('checked', false);
        }
        filterProducts();
    });

    // Industrias
    $('.industry_item').on('change', function () {
        if ($(this).is(':checked')) {
            $('.all_item').prop('checked', false);
        }
        filterProducts();
    });

    //-------------------------------------------------------------
    // 4. Filtrar inmediatamente si llegó un parámetro URL
    //-------------------------------------------------------------
    if (urlCategory || urlSubcategory || urlIndustry) {
        filterProducts();
    }

});
