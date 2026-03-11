@if($banners && $banners->count() > 0)
<section class="pt-6  pb-2">
    {{-- Outer: positions arrows over the carousel --}}
    <div class="relative" id="carouselOuter">

        {{-- Clipping wrapper: hides the parts of adjacent slides outside view --}}
        <div class="overflow-hidden" id="carouselContainer">
            <div class="relative flex transition-transform duration-500 ease-in-out" id="carousel">
                @foreach($banners as $banner)
                <div class="carousel-slide flex-shrink-0 w-[95%] sm:w-[90%] md:w-[85%] px-1 sm:px-2">
                    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white transition-all duration-500 ease-in-out">
                        <div class="relative w-full aspect-[4160/1624]">
                            @if($banner->link_url)
                                <a href="{{ $banner->link_url }}" target="_blank" class="block w-full h-full">
                                    <img src="{{ $banner->image_url }}"
                                         alt="{{ $banner->title ?? 'Banner' }}"
                                         class="w-full h-full object-cover">
                                </a>
                            @else
                                <img src="{{ $banner->image_url }}"
                                     alt="{{ $banner->title ?? 'Banner' }}"
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @if($banners->count() > 1)
        {{-- Arrows sit outside overflow:hidden so they render over the peek area --}}
        <button class="carousel-btn carousel-prev absolute left-2 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white/90 hover:bg-white text-slate-700 rounded-full shadow-md flex items-center justify-center transition-all duration-200 hover:scale-110">
            <i class="ri-arrow-left-s-line text-lg"></i>
        </button>
        <button class="carousel-btn carousel-next absolute right-2 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-white/90 hover:bg-white text-slate-700 rounded-full shadow-md flex items-center justify-center transition-all duration-200 hover:scale-110">
            <i class="ri-arrow-right-s-line text-lg"></i>
        </button>
        @endif
    </div>

    @if($banners->count() > 1)
    {{-- Pill dots --}}
    <div class="flex items-center justify-center gap-1.5 mt-4" id="carouselDots">
        @foreach($banners as $banner)
        <div class="carousel-dot rounded-full cursor-pointer transition-all duration-300"
             data-index="{{ $loop->index }}"
             style="height:6px; width:{{ $loop->first ? '24px' : '6px' }}; background-color:{{ $loop->first ? '#475569' : '#d1d5db' }};"></div>
        @endforeach
    </div>
    @endif
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('carouselContainer');
    const carousel  = document.getElementById('carousel');
    const slides    = document.querySelectorAll('.carousel-slide');
    const dots      = document.querySelectorAll('.carousel-dot');
    const prevBtn   = document.querySelector('.carousel-prev');
    const nextBtn   = document.querySelector('.carousel-next');

    if (!carousel || slides.length === 0) return;

    let current = 0;
    const total = slides.length;
    let autoPlay;

    function updateCarousel() {
        const cw    = container.offsetWidth;
        const slide = slides[current];
        // center the active slide inside the clipping container
        const translateX = cw / 2 - (slide.offsetLeft + slide.offsetWidth / 2);
        carousel.style.transform = `translateX(${translateX}px)`;

        // dim & shrink non-active cards
        slides.forEach((s, i) => {
            const card = s.firstElementChild;
            if (i === current) {
                card.style.opacity   = '1';
                card.style.transform = 'scale(1)';
            } else {
                card.style.opacity   = '0.55';
                card.style.transform = 'scale(0.93)';
            }
        });

        // update dots
        dots.forEach((d, i) => {
            d.style.width           = i === current ? '24px' : '6px';
            d.style.backgroundColor = i === current ? '#475569' : '#d1d5db';
        });
    }

    function goTo(index) {
        current = ((index % total) + total) % total;
        updateCarousel();
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    if (nextBtn) nextBtn.addEventListener('click', next);
    if (prevBtn) prevBtn.addEventListener('click', prev);

    dots.forEach((d, i) => d.addEventListener('click', () => goTo(i)));

    function startAutoPlay() { autoPlay = setInterval(next, 2000); }
    function stopAutoPlay()  { clearInterval(autoPlay); }

    if (total > 1) {
        startAutoPlay();
        carousel.addEventListener('mouseenter', stopAutoPlay);
        carousel.addEventListener('mouseleave', startAutoPlay);
    }

    // Swipe / touch
    let startX = 0;

    carousel.addEventListener('touchstart', e => {
        startX = e.touches[0].clientX;
        stopAutoPlay();
    }, { passive: true });

    carousel.addEventListener('touchend', e => {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) diff > 0 ? next() : prev();
        startAutoPlay();
    }, { passive: true });

    // Init
    updateCarousel();
    window.addEventListener('resize', updateCarousel);
});
</script>
@endif
