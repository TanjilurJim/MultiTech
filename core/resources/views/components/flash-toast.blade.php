{{-- resources/views/components/flash-toast.blade.php --}}
@if (session('success'))
    <div class="alert alert-success auto-hide-alert position-fixed"
         style="top:60px; right:60px; z-index:9999;
          font-size:1.05rem;  padding:.85rem 1.25rem; min-width:280px;
         
         ">
        {{ session('success') }}
    </div>

    @pushOnce('script')
        <script>
            setTimeout(() => {
                document.querySelectorAll('.auto-hide-alert')
                        .forEach(el => el.remove());
            }, 3000);
        </script>
    @endPushOnce
@endif
