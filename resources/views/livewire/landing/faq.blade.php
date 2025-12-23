<div class="bg-[#fafafa] min-h-screen">
    <div class="max-w-[1280px] mx-auto px-4 md:px-12 lg:px-16 py-6 md:py-8">
        <!-- Header: Back Button & Title -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-8">
            <!-- Back Button -->
            <a href="{{ route('home') }}" wire:navigate
                class="flex items-center justify-center gap-2 bg-[#313131] text-[#f6f6f6] px-5 md:px-6 py-2 rounded-2xl shadow hover:bg-[#404040] transition-colors w-max">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                </svg>
                <span class="montserrat-semibold text-sm md:text-base">Kembali</span>
            </a>

            <!-- Title -->
            <h1 class="montserrat-medium text-lg md:text-xl text-[#666666]">
                Frequently Asked Questions (FAQ)
            </h1>
        </div>

        <!-- FAQ List -->
        <div class="flex flex-col gap-8 md:gap-10">
            @foreach ($faqs as $index => $faq)
                <div class="flex flex-col gap-4 md:gap-5">
                    <!-- Question -->
                    <p class="montserrat-medium text-base md:text-lg text-[#525252]">
                        {{ $index + 1 }}. {{ $faq['question'] }}
                    </p>

                    <!-- Answer -->
                    <div
                        class="bg-[#eaeaea] border border-[#d4d4d4] rounded-[15px] px-4 md:px-5 py-3 md:py-4 min-h-[46px] flex items-center">
                        <p class="montserrat-regular text-sm md:text-base text-[#666666] text-justify leading-relaxed">
                            {{ $faq['answer'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
