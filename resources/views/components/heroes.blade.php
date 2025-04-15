<div class="relative w-full overflow-hidden">
    <div class="flex transition-transform duration-[1000ms] ease-in-out" id="hero-slider">
        @foreach($heroes as $hero)
            <div class="w-full flex-shrink-0">
                <img src="{{ asset('storage/heroes/' . $hero->image) }}" 
                     alt="{{ $hero->name }}" 
                     class="w-full h-[300px] md:h-[550px] object-cover rounded-xl">
            </div>
        @endforeach
    </div>

    <!-- Navigation -->
    <button onclick="prevSlide()" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-slate-100/50 px-3 py-2 rounded-full shadow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 sm:w-6 sm:h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
    </button>

    <button onclick="nextSlide()" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-slate-100/50 px-3 py-2 rounded-full shadow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 sm:w-6 sm:h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
        </svg>
    </button>


</div>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll("#hero-slider > div");

    function showSlide(index) {
        const slider = document.getElementById("hero-slider");
        if (index >= slides.length) currentSlide = 0;
        else if (index < 0) currentSlide = slides.length - 1;
        else currentSlide = index;

        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    setInterval(nextSlide, 5000); // auto slider
</script>
