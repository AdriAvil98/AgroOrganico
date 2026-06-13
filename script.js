document.addEventListener('DOMContentLoaded', () => {
    initHeroSlider();
    initProductsCarousel();
});

function initHeroSlider() {
    const track = document.querySelector('.slider-track');
    const slides = document.querySelectorAll('.slide');

    if (!track || slides.length === 0) return;

    let index = 0;
    slides[0].classList.add('active');

    if (slides.length < 2) return;

    function moveSlider() {
        index++;
        slides.forEach(slide => slide.classList.remove('active'));

        track.style.transition = 'transform 1.2s cubic-bezier(0.645, 0.045, 0.355, 1)';
        track.style.transform = `translateX(-${index * 100}vw)`;
        slides[index].classList.add('active');

        if (index === slides.length - 1) {
            setTimeout(() => {
                track.style.transition = 'none';
                index = 0;
                track.style.transform = 'translateX(0vw)';
                slides[slides.length - 1].classList.remove('active');
                slides[0].classList.add('active');
            }, 1250);
        }
    }

    setInterval(moveSlider, 6000);
}

function initProductsCarousel() {
    const section = document.querySelector('.featured-products-section');
    if (!section) return;

    const track = section.querySelector('.carousel-track');
    const cards = section.querySelectorAll('.prod-card');
    const nextBtn = section.querySelector('.next-btn');
    const prevBtn = section.querySelector('.prev-btn');
    const dotsContainer = section.querySelector('.carousel-dots');

    if (!track || cards.length === 0 || !nextBtn || !prevBtn) return;

    let index = 0;
    let maxIndex = 0;
    let step = 0;
    let autoPlayInterval; // Variable para el temporizador automático

    function getCardsPerView() {
        if (window.matchMedia('(max-width: 768px)').matches) return 1;
        if (window.matchMedia('(max-width: 992px)').matches) return 2;
        return 3;
    }

    function measure() {
        const firstCard = cards[0];
        const styles = window.getComputedStyle(track);
        const gap = parseFloat(styles.columnGap || styles.gap) || 0;

        step = firstCard.getBoundingClientRect().width + gap;
        maxIndex = Math.max(0, cards.length - getCardsPerView());
        index = Math.min(index, maxIndex);
    }

    function renderDots() {
        if (!dotsContainer) return;

        dotsContainer.innerHTML = '';
        for (let i = 0; i <= maxIndex; i++) {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot';
            dot.type = 'button';
            dot.setAttribute('aria-label', `Ir al producto ${i + 1}`);
            dot.addEventListener('click', () => {
                index = i;
                updateCarousel();
            });
            dotsContainer.appendChild(dot);
        }
    }

    function updateCarousel() {
        track.style.transform = `translateX(-${index * step}px)`;
        prevBtn.disabled = index === 0;
        nextBtn.disabled = index === maxIndex;

        if (dotsContainer) {
            dotsContainer.querySelectorAll('.carousel-dot').forEach((dot, dotIndex) => {
                dot.classList.toggle('active', dotIndex === index);
                dot.setAttribute('aria-current', dotIndex === index ? 'true' : 'false');
            });
        }
    }

    function refresh() {
        measure();
        renderDots();
        updateCarousel();
    }

    // Funciones del AutoPlay
    function autoSlide() {
        if (index < maxIndex) {
            index++;
        } else {
            index = 0; // Regresa al inicio si llega al final
        }
        updateCarousel();
    }

    function startAutoPlay() {
        // Mueve el carrusel cada 3500 milisegundos (4.5 segundos)
        autoPlayInterval = setInterval(autoSlide, 4500);
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
    }

    // Controles de botones
    nextBtn.addEventListener('click', () => {
        index = Math.min(index + 1, maxIndex);
        updateCarousel();
        // Reiniciar el temporizador para que no gire de golpe después del click
        stopAutoPlay();
        startAutoPlay();
    });

    prevBtn.addEventListener('click', () => {
        index = Math.max(index - 1, 0);
        updateCarousel();
        stopAutoPlay();
        startAutoPlay();
    });

    // Pausar si el mouse está encima del carrusel
    section.addEventListener('mouseenter', stopAutoPlay);
    section.addEventListener('mouseleave', startAutoPlay);

    window.addEventListener('resize', refresh);
    refresh();
    
    // Iniciar el giro automático al cargar la página
    startAutoPlay();
}
