<script>
  import { onMount } from 'svelte'
  import { ChevronDown, Sparkles, Zap, Shield } from 'lucide-svelte'
  
  let mounted = false
  let mouseX = 0
  let mouseY = 0
  
  onMount(() => {
    mounted = true
  })
  
  function handleMouseMove(event) {
    mouseX = event.clientX
    mouseY = event.clientY
  }
  
  function scrollToContent() {
    window.scrollTo({
      top: window.innerHeight,
      behavior: 'smooth'
    })
  }
</script>

<svelte:window on:mousemove={handleMouseMove} />

<section class="relative h-screen flex items-center justify-center overflow-hidden">
  <!-- Animated Background -->
  <div class="absolute inset-0">
    <!-- Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900"></div>
    
    <!-- Floating Particles -->
    {#if mounted}
      {#each Array(20) as _, i}
        <div 
          class="absolute w-2 h-2 bg-white/20 rounded-full animate-float"
          style="
            left: {Math.random() * 100}%;
            top: {Math.random() * 100}%;
            animation-delay: {Math.random() * 6}s;
            animation-duration: {6 + Math.random() * 4}s;
          "
        ></div>
      {/each}
    {/if}
    
    <!-- Interactive Glow Effect -->
    <div 
      class="absolute w-96 h-96 bg-gradient-to-r from-blue-500/30 to-purple-500/30 rounded-full blur-3xl transition-all duration-1000 ease-out"
      style="
        left: {mouseX / window.innerWidth * 100 - 12}%;
        top: {mouseY / window.innerHeight * 100 - 12}%;
      "
    ></div>
  </div>
  
  <!-- Content -->
  <div class="relative z-10 max-w-6xl mx-auto px-6 text-center">
    {#if mounted}
      <!-- Main Heading -->
      <div class="animate-fade-in">
        <h1 class="text-5xl md:text-7xl font-bold mb-6">
          <span class="text-gradient">Modern</span>
          <span class="text-white block">Web Development</span>
        </h1>
        
        <p class="text-xl md:text-2xl text-white/80 mb-8 max-w-3xl mx-auto leading-relaxed">
          Built with <span class="text-gradient font-semibold">Svelte</span>, 
          <span class="text-gradient font-semibold">Tailwind CSS</span>, and 
          <span class="text-gradient font-semibold">Mezzio PHP</span> for lightning-fast performance
        </p>
      </div>
      
      <!-- Feature Cards -->
      <div class="grid md:grid-cols-3 gap-6 mb-12 animate-slide-up" style="animation-delay: 0.3s;">
        <div class="glass rounded-xl p-6 hover:bg-white/20 transition-all duration-300 group">
          <Sparkles class="w-8 h-8 text-blue-400 mx-auto mb-4 group-hover:scale-110 transition-transform duration-300" />
          <h3 class="text-white font-semibold mb-2">Interactive</h3>
          <p class="text-white/70 text-sm">Reactive components with smooth animations</p>
        </div>
        
        <div class="glass rounded-xl p-6 hover:bg-white/20 transition-all duration-300 group">
          <Zap class="w-8 h-8 text-yellow-400 mx-auto mb-4 group-hover:scale-110 transition-transform duration-300" />
          <h3 class="text-white font-semibold mb-2">Lightning Fast</h3>
          <p class="text-white/70 text-sm">Optimized for performance and SEO</p>
        </div>
        
        <div class="glass rounded-xl p-6 hover:bg-white/20 transition-all duration-300 group">
          <Shield class="w-8 h-8 text-green-400 mx-auto mb-4 group-hover:scale-110 transition-transform duration-300" />
          <h3 class="text-white font-semibold mb-2">Secure</h3>
          <p class="text-white/70 text-sm">Built with security best practices</p>
        </div>
      </div>
      
      <!-- CTA Buttons -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center animate-slide-up" style="animation-delay: 0.6s;">
        <button class="btn-primary text-lg px-8 py-4">
          Get Started
        </button>
        <button class="btn-secondary text-lg px-8 py-4">
          View Demo
        </button>
      </div>
    {/if}
  </div>
  
  <!-- Scroll Indicator -->
  <button 
    on:click={scrollToContent}
    class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white/60 hover:text-white transition-colors duration-300 animate-bounce-slow"
  >
    <ChevronDown size={32} />
  </button>
</section>
