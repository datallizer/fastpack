$(".slickcard").slick({
  slidesToShow: 3,
  slidesToScroll: 1,
  autoplay: true,
  speed: 300,
  arrows: true,
  dots: true,
  responsive: [{
    breakpoint: 768,
    settings: {
      slidesToShow: 3,
      slidesToScroll: 2
    }
  }, {
    breakpoint: 520,
    settings: {
      slidesToShow: 2,
      slidesToScroll: 1
    }
  }],
  afterChange: function(event, slick, currentSlide) {
    // Espera a que se complete la animación del carrusel
    setTimeout(function() {
      renderCanvasForVisibleSlides();
    }, 300); // Ajusta el tiempo según sea necesario
  }
});

// Función para renderizar los canvas visibles
function renderCanvasForVisibleSlides() {
  $(".slickc").each(function(index, element) {
    var canvasId = $(element).find("canvas").attr("id");
    if (canvasId) {
      var pdfId = canvasId.replace("pdfCanvas", "");
      const pdfPath = "<?= $registro['path']; ?>"; // Usar la ruta correspondiente

      const canvas = document.getElementById(canvasId);
      const context = canvas.getContext("2d");

      // Carga el PDF en el canvas si no ha sido cargado ya
      const loadingTask = pdfjsLib.getDocument(pdfPath);
      loadingTask.promise.then(function(pdf) {
        pdf.getPage(1).then(function(page) {
          const viewport = page.getViewport({ scale: 0.5 });
          canvas.width = viewport.width;
          canvas.height = viewport.height;

          const renderContext = {
            canvasContext: context,
            viewport: viewport
          };
          page.render(renderContext);
        });
      }).catch(function(error) {
        console.error("Error al cargar el PDF: ", error);
      });
    }
  });
}
