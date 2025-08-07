import React, { useEffect, useState } from 'react'
import { motion } from 'framer-motion'
import ForestNavigation from './components/ForestNavigation'
import NatureHero from './components/NatureHero'
import FallingLeaves from './components/FallingLeaves'
import ForestFooter from './components/ForestFooter'

function App() {
  const [mounted, setMounted] = useState(false)

  useEffect(() => {
    setMounted(true)
    console.log('🌿 Forest ecosystem loaded - welcome to digital nature')
  }, [])

  if (!mounted) {
    return (
      <div className="min-h-screen bg-forest-dark flex items-center justify-center">
        <motion.div 
          className="text-center"
          initial={{ opacity: 0, scale: 0.8 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 2, ease: "easeOut" }}
        >
          <div className="text-6xl mb-4 animate-sway">🌲</div>
          <p className="nature-text text-xl font-calm">Forest is awakening...</p>
        </motion.div>
      </div>
    )
  }

  return (
    <div className="min-h-screen relative overflow-hidden">
      {/* Falling Leaves Background */}
      <FallingLeaves />
      
      {/* Forest Grid Overlay - subtle */}
      <div className="fixed inset-0 opacity-5 pointer-events-none">
        <div 
          className="w-full h-full" 
          style={{
            backgroundImage: `
              linear-gradient(rgba(77, 124, 89, 0.1) 1px, transparent 1px),
              linear-gradient(90deg, rgba(77, 124, 89, 0.1) 1px, transparent 1px)
            `,
            backgroundSize: '60px 60px'
          }}
        />
      </div>
      
      {/* Main Content */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 3, ease: "easeOut" }}
      >
        <ForestNavigation />
        <NatureHero />
        <ForestFooter />
      </motion.div>
      
      {/* Ambient Light Effect */}
      <div className="fixed inset-0 pointer-events-none">
        <motion.div 
          className="absolute top-1/4 left-1/4 w-96 h-96 bg-forest-light/10 rounded-full blur-3xl"
          animate={{
            scale: [1, 1.2, 1],
            opacity: [0.3, 0.5, 0.3],
          }}
          transition={{
            duration: 8,
            repeat: Infinity,
            ease: "easeInOut"
          }}
        />
        <motion.div 
          className="absolute bottom-1/4 right-1/4 w-64 h-64 bg-forest-sage/10 rounded-full blur-2xl"
          animate={{
            scale: [1.2, 1, 1.2],
            opacity: [0.2, 0.4, 0.2],
          }}
          transition={{
            duration: 10,
            repeat: Infinity,
            ease: "easeInOut"
          }}
        />
      </div>
    </div>
  )
}

export default App
