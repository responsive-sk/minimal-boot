<script>
  import { onMount } from 'svelte'
  
  let scrolled = false
  let mounted = false
  
  let ticking = false

  function handleScroll() {
    if (!ticking) {
      requestAnimationFrame(() => {
        const scrollY = window.scrollY
        scrolled = scrollY > 50

        // Update navigation glassmorphism with smooth transitions
        const nav = document.querySelector('nav')
        if (nav) {
          // Calculate opacity based on scroll position (0-100px range)
          const opacity = Math.min(scrollY / 100, 1)
          const borderOpacity = Math.min(scrollY / 150, 0.2)

          // Batch DOM writes to prevent forced reflow
          if (scrolled) {
            nav.style.cssText = `
              background: rgba(255, 255, 255, ${opacity * 0.1});
              backdrop-filter: blur(${opacity * 12}px);
              border-bottom: 1px solid rgba(255, 255, 255, ${borderOpacity});
              transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
            `
          } else {
            nav.style.cssText = `
              background: transparent;
              backdrop-filter: none;
              border-bottom: none;
              transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
            `
          }
        }
        ticking = false
      })
      ticking = true
    }
  }
  
  onMount(() => {
    mounted = true
  })
</script>

<svelte:window on:scroll={handleScroll} />

<!-- Scroll effects are applied via DOM manipulation -->
