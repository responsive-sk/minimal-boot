import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import './index.css'

function initForestApp() {
  const target = document.getElementById('react-app')
  if (!target) {
    console.error('React mount point #react-app not found')
    return
  }
  
  const root = ReactDOM.createRoot(target)
  root.render(<App />)
  
  console.log('🌲 Forest Calm React theme initialized - breathe deeply...')
  return root
}

function waitForElement() {
  const target = document.getElementById('react-app')
  if (target) {
    initForestApp()
  } else {
    console.log('🍃 Waiting for forest to grow...')
    setTimeout(waitForElement, 50)
  }
}

// Start the forest
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', waitForElement)
} else {
  waitForElement()
}
