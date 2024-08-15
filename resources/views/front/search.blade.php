@extends('layout.index')
@section('content')
  <main class="bg-[#FAFAFA] max-w-[640px] mx-auto min-h-screen relative flex flex-col has-[#CTA-nav]:pb-[120px] has-[#Bottom-nav]:pb-[120px]">
    <div id="Top-nav" class="flex items-center justify-between px-4 pt-5">
      <a href="{{route('front.index')}}">
        <div class="w-10 h-10 flex shrink-0">
          <img src="assets/images/icons/back.svg" alt="icon">
        </div>
      </a>
      <div class="flex flex-col w-fit text-center">
        <h1 class="font-semibold text-lg leading-[27px]">Gold Wash</h1>
        <p class="text-sm leading-[21px] text-[#909DBF]">Kota Jakarta, 183 Stores</p>
      </div>
      <button class="w-10 h-10 flex shrink-0">
        <img src="assets/images/icons/filter.svg" alt="icon">
      </button>
    </div>
    <section id="Store-list" class="flex flex-col gap-6 px-4 mt-[30px]">
        @forelse($store as $key => $value)
        <a href="{{route('front.details', $value->slug)}}" class="card">
            <div class="flex flex-col gap-4 rounded-[20px] ring-1 ring-[#E9E8ED] pb-4 bg-white overflow-hidden transition-all duration-300 hover:ring-2 hover:ring-[#FF8E62]">
              <div class="w-full h-[120px] flex shrink-0 overflow-hidden relative">
                <img src="{{Storage::url($value->thumbnail)}}" class="w-full h-full object-cover" alt="thumbnail">
                <p class="rounded-full p-[6px_10px] bg-[#41BE64] w-fit h-fit font-bold text-[10px] leading-[15px] text-white absolute top-4 right-4">OPEN NOW</p>
              </div>
              <div class="flex items-center justify-between gap-4 px-4">
                <div class="title flex flex-col gap-[6px]">
                  <div class="flex items-center gap-1">
                    <h1 class="font-semibold w-fit">{{$value->name}}</h1>
                    <div class="w-[18px] h-[18px] flex shrink-0">
                      <img src="assets/images/icons/verify.svg" alt="verified">
                    </div>
                  </div>
                  <div class="flex items-center gap-[2px]">
                    <div class="w-4 h-4 flex shrink-0">
                      <img src="assets/images/icons/location.svg" alt="icon">
                    </div>
                    <p class="text-sm leading-[21px] text-[#909DBF]">{{Str::substr($value->address,0,10)}}... , {{$value->city->name}}</p>
                  </div>
                </div>
                <div class="rating flex flex-col gap-[6px]">
                  <div class="flex items-center justify-end text-right gap-[6px]">
                    <div class="w-[18px] h-[18px] flex shrink-0">
                      <img src="assets/images/icons/Star 1.svg" alt="verified">
                    </div>
                    <h1 class="font-semibold w-fit">4.8</h1>
                  </div>
                  <div class="flex items-center justify-end text-right gap-[2px]">
                    <p class="text-sm leading-[21px] text-[#909DBF]">145 Reviews</p>
                  </div>
                </div>
              </div>
            </div>
          </a>
        @empty
            <p>Belum ada layanan tersebut</p>
        @endforelse
      
    </section>
  </main>
@endsection