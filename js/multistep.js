// jQuery time
var current_fs, next_fs, previous_fs; // Fieldsets
var left, opacity, scale; // Fieldset properties for animation
var animating; // Flag to prevent quick multi-click glitches

// Botón "Next" para avanzar al siguiente paso
$(".next").click(function () {
    if (animating) return false;
    animating = true;

    current_fs = $(this).parent(); // Fieldset actual
    next_fs = $(this).parent().next(); // Siguiente fieldset

    // Activar el siguiente paso en la barra de progreso
    $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

    // Mostrar el siguiente fieldset
    next_fs.show();

    // Ocultar el fieldset actual con animación
    current_fs.animate({ opacity: 0 }, {
        step: function (now, mx) {
            // Reducir la escala del fieldset actual
            scale = 1 - (1 - now) * 0.2;
            // Mover el siguiente fieldset desde la derecha
            left = (now * 50) + "%";
            // Incrementar la opacidad del siguiente fieldset
            opacity = 1 - now;
            current_fs.css({
                'transform': 'scale(' + scale + ')',
                'position': 'absolute'
            });
            next_fs.css({ 'left': left, 'opacity': opacity });
        },
        duration: 800,
        complete: function () {
            current_fs.hide();
            animating = false;
        },
        easing: 'easeInOutBack' // Efecto de animación
    });
});

// Botón "Previous" para regresar al paso anterior
$(".previous").click(function () {
    if (animating) return false;
    animating = true;

    current_fs = $(this).parent(); // Fieldset actual
    previous_fs = $(this).parent().prev(); // Fieldset anterior

    // Desactivar el paso actual en la barra de progreso
    $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

    // Mostrar el fieldset anterior
    previous_fs.show();

    // Ocultar el fieldset actual con animación
    current_fs.animate({ opacity: 0 }, {
        step: function (now, mx) {
            // Incrementar la escala del fieldset anterior
            scale = 0.8 + (1 - now) * 0.2;
            // Mover el fieldset actual hacia la derecha
            left = ((1 - now) * 50) + "%";
            // Incrementar la opacidad del fieldset anterior
            opacity = 1 - now;
            current_fs.css({ 'left': left });
            previous_fs.css({ 'transform': 'scale(' + scale + ')', 'opacity': opacity });
        },
        duration: 800,
        complete: function () {
            current_fs.hide();
            animating = false;
        },
        easing: 'easeInOutBack' // Efecto de animación
    });
});

// Botón "Submit" para enviar el formulario
$(".submit").click(function () {
    alert("Formulario enviado correctamente.");
    return true; // Cambia a `true` para permitir el envío del formulario
});