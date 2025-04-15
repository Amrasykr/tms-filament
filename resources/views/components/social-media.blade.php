<div class="flex space-x-3">
    @foreach($socialMedias as $socialMedia)
        <a href="{{ $socialMedia->link }}" target="_blank"
            class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 transition ease-out duration-300 p-1.5">
            {!! $socialMedia->icon !!}
        </a>
    @endforeach
</div>
