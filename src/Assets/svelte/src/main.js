import App from './App.svelte'
import './style.css'

function initApp()
{
    const target = document.getElementById('svelte-app')
    if (!target) {
        console.error('Svelte mount point #svelte-app not found')
        return
    }

  // For SEO-friendly version, we don't clear content
  // Svelte components will be overlays/enhancements

    const app = new App({
        target: target
    })

    console.log('Svelte enhancements initialized successfully!')
    return app
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp)
} else {
    initApp()
}
