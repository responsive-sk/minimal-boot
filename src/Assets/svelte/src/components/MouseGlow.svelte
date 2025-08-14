<script>
  import { onMount } from 'svelte'

  let mouseX = 0
  let mouseY = 0
  let mounted = false

  // Cache window dimensions to prevent forced reflow
  let windowWidth = 1920
  let windowHeight = 1080

  function handleMouseMove(event) {
    mouseX = event.clientX
    mouseY = event.clientY
  }

  function handleResize() {
    windowWidth = window.innerWidth
    windowHeight = window.innerHeight
  }

  onMount(() => {
    mounted = true
    windowWidth = window.innerWidth
    windowHeight = window.innerHeight
  })
</script>

<svelte:window on:mousemove={handleMouseMove} on:resize={handleResize} />

<!-- Mouse tracking glow effect -->
{#if mounted}
  <div class="fixed inset-0 pointer-events-none z-5">
    <div 
      class="absolute w-96 h-96 bg-gradient-to-r from-blue-500/30 to-purple-500/30 rounded-full blur-3xl transition-all duration-1000 ease-out"
      style="
        left: {mouseX / windowWidth * 100 - 12}%;
        top: {mouseY / windowHeight * 100 - 12}%;
      "
    ></div>
  </div>
{/if}
