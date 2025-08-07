import { createApp } from 'vue'
import App from './App.vue'
import './style.css'

function initVueApp() {
  const target = document.getElementById('vue-app')
  if (!target) {
    console.error('Vue mount point #vue-app not found')
    return
  }

  const app = createApp(App)
  app.mount(target)

  console.log('🚀 Vue Cyberpunk theme initialized!')
  return app
}

function waitForElement() {
  const target = document.getElementById('vue-app')
  if (target) {
    initVueApp()
  } else {
    console.log('Waiting for #vue-app element...')
    setTimeout(waitForElement, 50)
  }
}

// Start waiting for element
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', waitForElement)
} else {
  waitForElement()
}
