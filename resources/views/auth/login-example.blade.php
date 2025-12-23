{{-- EXAMPLE: How to use a real image instead of CSS logo --}}
{{-- This is just a reference file, not used in the actual app --}}

{{-- If you have a logo image at: public/images/logos/umpsa-logo.png --}}
{{-- Replace the logo section in login.blade.php with this: --}}

<!-- University Logo -->
<div class="flex justify-center my-8">
    <img 
        src="{{ asset('images/logos/umpsa-logo.png') }}" 
        alt="UMPSA Logo"
        class="w-48 h-56 object-contain"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
    >
    {{-- Fallback if image doesn't exist --}}
    <div class="relative hidden">
        <!-- Your current CSS logo code here -->
    </div>
</div>

