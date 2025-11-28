<div class="bg-[#fafafa] min-h-screen">
    <div class="max-w-[1280px] mx-auto px-[65px] py-[30px]">
        <!-- Header: Back Button & Title -->
        <div class="flex items-center gap-[15px] mb-[30px]">
            <!-- Back Button -->
            <a href="{{ route('home') }}" wire:navigate
                class="flex items-center gap-[5px] bg-[#313131] text-[#f6f6f6] px-[25px] py-[10px] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] hover:bg-[#404040] transition-colors">
                <svg class="w-[20px] h-[20px]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                </svg>
                <span class="montserrat-semibold text-[16px]">Kembali</span>
            </a>

            <!-- Title -->
            <h1 class="montserrat-medium text-[20px] text-[#666666]">
                Frequently Asked Questions (FAQ)
            </h1>
        </div>

        <!-- FAQ List -->
        <div class="flex flex-col gap-[30px]">
            @foreach ($faqs as $index => $faq)
                <div class="flex flex-col gap-[19px]">
                    <!-- Question -->
                    <p class="montserrat-medium text-[16px] text-[#525252]">
                        {{ $index + 1 }}. {{ $faq['question'] }}
                    </p>

                    <!-- Answer -->
                    <div
                        class="bg-[#eaeaea] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px] min-h-[46px] flex items-center">
                        <p class="montserrat-regular text-[16px] text-[#666666] text-justify">
                            {{ $faq['answer'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
