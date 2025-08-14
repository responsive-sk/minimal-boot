<script>
  import { onMount } from 'svelte'
  
  let scrolled = false
  let mounted = false
  
  let ticking = false

  // Cache nav element to avoid repeated queries
  let navElement = null
  let lastScrollState = null

  function handleScroll() {
    if (!ticking) {
      requestAnimationFrame(() => {
        // Batch all DOM reads first
        const scrollY = window.scrollY
        const newScrolled = scrollY > 50

        // Only update if state changed to prevent unnecessary DOM writes
        if (newScrolled !== lastScrollState) {
          scrolled = newScrolled
          lastScrollState = newScrolled

          // Cache nav element on first use
          if (!navElement) {
            navElement = document.querySelector('nav')
          }

          if (navElement) {
            // Calculate values once
            const opacity = Math.min(scrollY / 100, 1)
            const borderOpacity = Math.min(scrollY / 150, 0.2)

            // Single DOM write with transform for better performance
            if (scrolled) {
              navElement.style.cssText = `
                background: rgba(255, 255, 255, ${opacity * 0.1});
                backdrop-filter: blur(${opacity * 12}px);
                border-bottom: 1px solid rgba(255, 255, 255, ${borderOpacity});
                transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
                will-change: background, backdrop-filter, border-bottom;
              `
            } else {
              navElement.style.cssText = `
                background: transparent;
                backdrop-filter: none;
                border-bottom: none;
                transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
                will-change: auto;
              `
            }
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
