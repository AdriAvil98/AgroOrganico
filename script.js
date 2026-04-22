window.onload = function() {
    const track = document.querySelector('.slider-track');
    const slides = document.querySelectorAll('.slide');
    let index = 0;

    // Inicializar el primer slide
    slides[0].classList.add('active');

    function moveSlider() {
        // 1. Avanzamos el índice
        index++;
        
        // 2. Quitamos la clase active de todos para limpiar
        slides.forEach(s => s.classList.remove('active'));

        // 3. Aplicamos la animación de movimiento
        track.style.transition = "transform 1.2s cubic-bezier(0.645, 0.045, 0.355, 1)";
        track.style.transform = `translateX(-${index * 100}vw)`;

        // 4. Encendemos el texto del slide actual (incluido el clon)
        slides[index].classList.add('active');

        // 5. SI LLEGAMOS AL CLON (Último slide)
        if (index === slides.length - 1) {
            // Esperamos a que termine la animación (1.2s) + un pequeño margen
            setTimeout(() => {
                // DESACTIVAMOS la transición para el salto invisible
                track.style.transition = "none";
                index = 0;
                track.style.transform = `translateX(0vw)`;
                
                // MANTENEMOS el texto encendido en el original 
                // para que no haya parpadeo visual
                slides[slides.length - 1].classList.remove('active');
                slides[0].classList.add('active');
            }, 1250); 
        }
    }

    // Intervalo de 6 segundos
    setInterval(moveSlider, 6000);
};