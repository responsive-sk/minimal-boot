<script>
  import { Menu, X } from 'lucide-svelte'
  
  let mobileMenuOpen = false
  let scrolled = false
  
  function handleScroll() {
    scrolled = window.scrollY > 50
  }
  
  function toggleMobileMenu() {
    mobileMenuOpen = !mobileMenuOpen
  }
</script>

<svelte:window on:scroll={handleScroll} />

<nav class="fixed w-full top-0 z-50 transition-all duration-300" 
     class:glass={scrolled} 
     class:bg-transparent={!scrolled}>
  <div class="max-w-7xl mx-auto px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <!-- Logo -->
      <div class="flex items-center">
        <a href="/" class="flex items-center space-x-2 group">
          <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <span class="text-white font-bold text-sm">MB</span>
          </div>
          <span class="text-white font-semibold text-lg">Minimal Boot</span>
        </a>
      </div>
      
      <!-- Desktop Menu -->
      <div class="hidden md:flex items-center space-x-8">
        <a href="/" class="text-white/80 hover:text-white transition-colors duration-300">Home</a>
        <a href="/about" class="text-white/80 hover:text-white transition-colors duration-300">About</a>
        <a href="/contact" class="text-white/80 hover:text-white transition-colors duration-300">Contact</a>
        <button class="btn-primary">Get Started</button>
      </div>
      
      <!-- Mobile Menu Button -->
      <div class="md:hidden">
        <button on:click={toggleMobileMenu} class="text-white p-2">
          {#if mobileMenuOpen}
            <X size={24} />
          {:else}
            <Menu size={24} />
          {/if}
        </button>
      </div>
    </div>
    
    <!-- Mobile Menu -->
    {#if mobileMenuOpen}
      <div class="md:hidden glass rounded-lg mt-2 p-4 space-y-4">
        <a href="/" class="block text-white/80 hover:text-white transition-colors duration-300">Home</a>
        <a href="/about" class="block text-white/80 hover:text-white transition-colors duration-300">About</a>
        <a href="/contact" class="block text-white/80 hover:text-white transition-colors duration-300">Contact</a>
        <button class="btn-primary w-full">Get Started</button>
      </div>
    {/if}
  </div>
</nav>
